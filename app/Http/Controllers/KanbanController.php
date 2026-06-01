<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;

class KanbanController extends Controller
{
    public function __construct(protected TaskService $taskService) {}

    /**
     * Show Kanban board for a project
     */
    public function show(Project $project)
    {
        $kanbanData = $this->taskService->getKanbanData($project);
        $isTeamLeader = $project->team->users()
            ->where('user_id', auth()->id())
            ->wherePivot('role', 'leader')
            ->exists();
        
        return view('projects.kanban', compact('project', 'kanbanData', 'isTeamLeader'));
    }

    /**
     * AJAX endpoint to update task position and status
     */
    public function updatePosition(Request $request, Task $task)
    {
        $project = $task->project;
        $isTeamLeader = $project->team->users()
            ->where('user_id', auth()->id())
            ->wherePivot('role', 'leader')
            ->exists();
        $isAssignedUser = $task->assigned_to === auth()->id();

        // Only team leader or assigned user can move tasks
        if (!$isTeamLeader && !$isAssignedUser) {
            \Log::error("Kanban move failed auth", [
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'assigned_to' => $task->assigned_to,
                'isTeamLeader' => $isTeamLeader,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to move this task',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,review,done',
            'position' => 'required|integer|min:0',
        ]);

        $this->taskService->moveTask($task, $validated['status'], $validated['position']);

        return response()->json([
            'success' => true,
            'message' => 'Task updated',
        ]);
    }

    /**
     * AJAX endpoint to create task inline
     */
    public function store(Request $request, Project $project)
    {
        // Only team leaders can create tasks
        $isTeamLeader = $project->team->users()
            ->where('user_id', auth()->id())
            ->wherePivot('role', 'leader')
            ->exists();

        if (!$isTeamLeader) {
            return response()->json([
                'success' => false,
                'message' => 'Only team leaders can create tasks',
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:todo,in_progress,review,done',
            'priority' => 'required|in:low,medium,high,critical',
            'description' => 'nullable|string',
        ]);

        $task = $this->taskService->createTask($validated, $project);
        $task->load('assignedUser', 'creator');

        $html = view('projects.task-card', compact('task'))->render();

        return response()->json([
            'success' => true,
            'task' => $task,
            'html' => $html,
        ]);
    }

    /**
     * Get Kanban data as JSON for polling/updates
     */
    public function getData(Project $project)
    {
        $tasks = $project->tasks()->with(['assignedUser', 'creator'])->get();
        
        return response()->json([
            'success' => true,
            'tasks' => $tasks->map(fn($task) => [
                'id' => $task->id,
                'status' => $task->status,
                'position' => $task->position,
            ]),
        ]);
    }
}
