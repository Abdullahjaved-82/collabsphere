<?php

namespace App\Services;

use App\Models\AiSuggestion;
use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Generate a task breakdown for a project using Groq API.
     */
    public function generateTaskBreakdown(Project $project): array
    {
        $apiKey = config('groq.api_key');
        $model = config('groq.model');
        $baseUrl = config('groq.base_url');

        if (empty($apiKey) || $apiKey === 'your_key_here') {
            throw new \RuntimeException('Groq API key is not configured. Please set GROQ_API_KEY in your .env file.');
        }

        $systemPrompt = $this->getSystemPrompt();
        $userPrompt = $this->buildPrompt($project);

        $response = Http::withoutVerifying()
            ->withToken($apiKey)
            ->timeout(30)
            ->post("{$baseUrl}/chat/completions", [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 2048,
            ]);

        if ($response->status() === 429) {
            $retryAfter = $response->header('Retry-After', 30);
            throw new \RuntimeException("RATE_LIMIT:{$retryAfter}");
        }

        if ($response->failed()) {
            Log::error('Groq API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Failed to connect to AI service. Status: ' . $response->status());
        }

        $content = $response->json('choices.0.message.content', '');

        // Parse JSON from the response
        $tasks = $this->parseTasksFromResponse($content);

        // Store raw response in ai_suggestions table
        $suggestion = AiSuggestion::create([
            'project_id' => $project->id,
            'prompt_used' => $userPrompt,
            'response_json' => $tasks,
            'status' => 'pending',
        ]);

        return [
            'suggestion_id' => $suggestion->id,
            'tasks' => $tasks,
            'raw_content' => $content,
        ];
    }

    /**
     * Suggest the best assignee for a task based on team workload.
     */
    public function suggestTaskAssignment(Task $task, Team $team): string
    {
        $apiKey = config('groq.api_key');
        $model = config('groq.model');
        $baseUrl = config('groq.base_url');

        if (empty($apiKey) || $apiKey === 'your_key_here') {
            return 'AI service not configured.';
        }

        $members = $team->users()->withCount(['assignedTasks' => function ($q) {
            $q->whereIn('status', ['todo', 'in_progress', 'review']);
        }])->get();

        $memberList = $members->map(function ($m) {
            return "{$m->name} — {$m->assigned_tasks_count} active tasks";
        })->join("\n");

        $prompt = "Given this task:\nTitle: {$task->title}\nDescription: {$task->description}\nPriority: {$task->priority}\n\nTeam members and their current workload:\n{$memberList}\n\nWho should this task be assigned to and why? Reply in one short sentence.";

        $response = Http::withoutVerifying()
            ->withToken($apiKey)
            ->timeout(15)
            ->post("{$baseUrl}/chat/completions", [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a project management assistant. Suggest the best person to assign a task to based on workload balance and relevance. Be concise.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.5,
                'max_tokens' => 150,
            ]);

        if ($response->failed()) {
            return 'Could not get AI suggestion at this time.';
        }

        return $response->json('choices.0.message.content', 'No suggestion available.');
    }

    /**
     * Get the system prompt for task breakdown.
     */
    private function getSystemPrompt(): string
    {
        return <<<PROMPT
You are a project management expert helping student teams.
Given a project, generate a realistic task breakdown as JSON.
Return ONLY a JSON array. Each task: {
  "title": string (max 60 chars),
  "description": string (1-2 sentences),
  "priority": "low"|"medium"|"high"|"critical",
  "estimated_hours": number,
  "suggested_status": "todo",
  "category": "frontend"|"backend"|"design"|"research"|"testing"|"documentation",
  "assigned_to": integer|null (ID of the suggested team member based on specialty, or null if no one matches)
}
Generate 6-10 tasks. Be specific to the project domain.
Do not include any text before or after the JSON array.
PROMPT;
    }

    /**
     * Build the user prompt with project context.
     */
    private function buildPrompt(Project $project): string
    {
        $project->loadMissing('team.users', 'tasks');

        $teamSize = $project->team ? $project->team->users->count() : 1;
        $existingTasks = $project->tasks->count();
        $deadline = $project->deadline ? $project->deadline->format('Y-m-d') : 'No deadline set';

        $membersList = "None";
        if ($project->team) {
            $membersList = $project->team->users->map(function ($u) {
                $specialty = $u->specialty ?: 'Unspecified';
                return "- ID: {$u->id}, Name: {$u->name}, Specialty: {$specialty}";
            })->join("\n");
        }

        return <<<PROMPT
Project: {$project->title}
Description: {$project->description}
Deadline: {$deadline}
Team Size: {$teamSize} members
Existing Tasks: {$existingTasks}

Team Members Available for Assignment:
{$membersList}

Read the project description carefully. If there is no clear description, use your own thinking to infer the necessary tasks based on the title.
Generate a comprehensive task breakdown for this project. Consider the team size and deadline when estimating hours. 
For each task, recommend the best partner/team member suited for it according to their Specialty or field. If you cannot find any user with the required expertise, you must leave it unassigned (null).
PROMPT;
    }

    /**
     * Parse tasks array from AI response content.
     */
    private function parseTasksFromResponse(string $content): array
    {
        // Try to extract JSON array from the response
        $content = trim($content);

        // Remove markdown code fences if present
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/', $content, $matches)) {
            $content = $matches[1];
        }

        // Try direct JSON decode
        $tasks = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($tasks)) {
            return $this->validateTasks($tasks);
        }

        // Try to find JSON array in the content
        if (preg_match('/\[[\s\S]*\]/', $content, $matches)) {
            $tasks = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($tasks)) {
                return $this->validateTasks($tasks);
            }
        }

        throw new \RuntimeException('PARSE_ERROR:' . $content);
    }

    /**
     * Validate and normalize parsed tasks.
     */
    private function validateTasks(array $tasks): array
    {
        $validPriorities = ['low', 'medium', 'high', 'critical'];
        $validCategories = ['frontend', 'backend', 'design', 'research', 'testing', 'documentation'];

        return array_map(function ($task) use ($validPriorities, $validCategories) {
            return [
                'title' => substr($task['title'] ?? 'Untitled Task', 0, 60),
                'description' => $task['description'] ?? '',
                'priority' => in_array($task['priority'] ?? '', $validPriorities) ? $task['priority'] : 'medium',
                'estimated_hours' => is_numeric($task['estimated_hours'] ?? null) ? (float) $task['estimated_hours'] : 2,
                'suggested_status' => 'todo',
                'category' => in_array($task['category'] ?? '', $validCategories) ? $task['category'] : 'backend',
                'assigned_to' => is_numeric($task['assigned_to'] ?? null) ? (int) $task['assigned_to'] : null,
            ];
        }, $tasks);
    }
}
