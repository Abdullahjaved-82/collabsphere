<?php

namespace App\Http\Controllers;

use App\Models\AiSuggestion;
use App\Models\Project;
use App\Services\AIService;
use App\Services\TaskService;
use Illuminate\Http\Request;

class AIController extends Controller
{
    public function __construct(
        protected AIService $aiService,
        protected TaskService $taskService,
    ) {}

    /**
     * Show the AI Assistant page for a project.
     */
    public function index(Project $project)
    {
        $project->load('team.users', 'tasks');

        $isTeamLeader = auth()->user()->teams()
            ->where('team_id', $project->team_id)
            ->wherePivot('role', 'leader')
            ->exists();

        $previousSuggestions = $project->aiSuggestions()
            ->latest('created_at')
            ->take(5)
            ->get();

        return view('projects.ai-assistant', compact('project', 'isTeamLeader', 'previousSuggestions'));
    }

    /**
     * Generate a task breakdown using AI.
     */
    public function generateBreakdown(Request $request, Project $project)
    {
        // Only team leaders can generate suggestions
        $isTeamLeader = auth()->user()->teams()
            ->where('team_id', $project->team_id)
            ->wherePivot('role', 'leader')
            ->exists();

        if (!$isTeamLeader) {
            return response()->json([
                'success' => false,
                'error' => 'permission_denied',
                'message' => 'Only team leaders can use the AI assistant.',
            ], 403);
        }

        try {
            $result = $this->aiService->generateTaskBreakdown($project);

            return response()->json([
                'success' => true,
                'suggestion_id' => $result['suggestion_id'],
                'tasks' => $result['tasks'],
            ]);
        } catch (\RuntimeException $e) {
            $message = $e->getMessage();

            // Handle rate limit
            if (str_starts_with($message, 'RATE_LIMIT:')) {
                $retryAfter = (int) str_replace('RATE_LIMIT:', '', $message);
                return response()->json([
                    'success' => false,
                    'error' => 'rate_limit',
                    'retry_after' => $retryAfter,
                    'message' => 'API rate limit reached. Please try again shortly.',
                ], 429);
            }

            // Handle parse error
            if (str_starts_with($message, 'PARSE_ERROR:')) {
                $rawContent = str_replace('PARSE_ERROR:', '', $message);
                return response()->json([
                    'success' => false,
                    'error' => 'parse_error',
                    'raw_content' => $rawContent,
                    'message' => 'Could not parse structured response from AI.',
                ], 200);
            }

            // Handle API key missing
            if (str_contains($message, 'not configured')) {
                return response()->json([
                    'success' => false,
                    'error' => 'no_api_key',
                    'message' => $message,
                ], 200);
            }

            // Generic error
            return response()->json([
                'success' => false,
                'error' => 'api_error',
                'message' => $message,
            ], 500);
        }
    }

    /**
     * Accept suggestions and bulk-create tasks in Kanban.
     */
    public function acceptSuggestions(Request $request, Project $project)
    {
        $validated = $request->validate([
            'suggestion_id' => 'required|integer|exists:ai_suggestions,id',
            'tasks' => 'required|array',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.priority' => 'required|in:low,medium,high,critical',
        ]);

        $isTeamLeader = auth()->user()->teams()
            ->where('team_id', $project->team_id)
            ->wherePivot('role', 'leader')
            ->exists();

        if (!$isTeamLeader) {
            return response()->json([
                'success' => false,
                'message' => 'Only team leaders can accept AI suggestions.',
            ], 403);
        }

        $createdTasks = [];

        foreach ($validated['tasks'] as $taskData) {
            $task = $this->taskService->createTask([
                'title' => $taskData['title'],
                'description' => $taskData['description'] ?? '',
                'priority' => $taskData['priority'],
                'status' => 'todo',
            ], $project);

            $createdTasks[] = $task;
        }

        // Mark suggestion as accepted
        AiSuggestion::where('id', $validated['suggestion_id'])->update(['status' => 'accepted']);

        return response()->json([
            'success' => true,
            'message' => count($createdTasks) . ' tasks created successfully!',
            'tasks_created' => count($createdTasks),
        ]);
    }

    /**
     * Reject an individual AI suggestion.
     */
    public function rejectSuggestion(Request $request, int $suggestionId)
    {
        $suggestion = AiSuggestion::findOrFail($suggestionId);

        $suggestion->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Suggestion rejected.',
        ]);
    }
}
