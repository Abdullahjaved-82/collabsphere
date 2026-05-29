<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function create(Project $project)
    {
        return view('projects.task-create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,review,done',
            'priority' => 'required|in:low,medium,high,critical',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['project_id'] = $project->id;
        $validated['created_by'] = auth()->id();
        $validated['position'] = Task::where('project_id', $project->id)->max('position') + 1;

        Task::create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Task created successfully!');
    }

    public function show(Task $task)
    {
        return view('projects.task-detail', compact('task'));
    }

    public function edit(Task $task)
    {
        $project = $task->project;
        return view('projects.task-edit', compact('task', 'project'));
    }

    public function update(Request $request, Task $task)
    {
        $project = $task->project;
        $isTeamLeader = auth()->user()->teams()
            ->where('team_id', $project->team_id)
            ->wherePivot('role', 'leader')
            ->exists();
        $isAssignedUser = $task->assigned_to === auth()->id();

        // Only team leader or assigned user can update task
        if (!$isTeamLeader && !$isAssignedUser) {
            return redirect()->back()
                ->with('error', 'You do not have permission to update this task');
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:todo,in_progress,review,done',
            'priority' => 'sometimes|required|in:low,medium,high,critical',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task->update($validated);

        return redirect()->back()
            ->with('success', 'Task updated successfully!');
    }

    public function destroy(Task $task)
    {
        $project = $task->project;
        $task->delete();

        return redirect()->route('projects.show', $project)
            ->with('success', 'Task deleted successfully!');
    }

    public function claim(Task $task)
    {
        if ($task->assigned_to !== null) {
            return redirect()->back()->with('error', 'Task is already assigned');
        }

        if ($task->requested_by !== null) {
            return redirect()->back()->with('error', 'Task claim already requested - waiting for approval');
        }

        $task->update(['requested_by' => auth()->id()]);

        return redirect()->back()->with('success', 'Task claim requested! Waiting for team leader approval.');
    }

    public function approveClaim(Task $task)
    {
        if ($task->requested_by === null) {
            return redirect()->back()->with('error', 'No pending claim for this task');
        }

        $task->update(['assigned_to' => $task->requested_by, 'requested_by' => null]);

        return redirect()->back()->with('success', 'Task assignment approved!');
    }
}
