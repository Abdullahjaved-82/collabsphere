<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Team;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projectService)
    {
    }

    public function index(): View
    {
        $teams = auth()->user()->teams;
        $projects = collect();

        foreach ($teams as $team) {
            $projects = $projects->concat($this->projectService->getProjectsForTeam($team->id));
        }

        return view('projects.index', compact('projects', 'teams'));
    }

    public function create(): View
    {
        // Only show teams where the user is the leader
        $teams = auth()->user()->teams()->wherePivot('role', 'leader')->get();

        return view('projects.create', compact('teams'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'team_id' => ['required', 'exists:teams,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:planning,active,completed'],
            'deadline' => ['nullable', 'date'],
        ]);

        $team = Team::findOrFail($validated['team_id']);

        if (!$team->users()->where('user_id', auth()->id())->wherePivot('role', 'leader')->exists()) {
            abort(403, 'Only team leaders can create projects for this team.');
        }

        $project = $this->projectService->createProject($validated, $team, auth()->user());

        return redirect()->route('projects.show', $project)->with('success', 'Project created successfully!');
    }

    public function show(Project $project): View
    {
        $project->load('team', 'creator', 'tasks');

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        $teams = auth()->user()->teams;

        return view('projects.edit', compact('project', 'teams'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:planning,active,completed'],
            'deadline' => ['nullable', 'date'],
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully!');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully!');
    }
}
