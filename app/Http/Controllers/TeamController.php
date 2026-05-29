<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TeamController extends Controller
{
    public function __construct(private TeamService $teamService)
    {
    }

    public function index(): View
    {
        $teams = auth()->user()->teams()->with('creator')->get();

        return view('teams.index', compact('teams'));
    }

    public function create(): View
    {
        return view('teams.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $team = $this->teamService->createTeam($validated, auth()->user());

        return redirect()->route('teams.show', $team)->with('success', 'Team created successfully!');
    }

    public function show(Team $team): View
    {
        $team->load('users', 'projects');

        return view('teams.show', compact('team'));
    }

    public function join(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invite_code' => ['required', 'string', 'size:8'],
        ]);

        try {
            $team = $this->teamService->joinTeam($validated['invite_code'], auth()->user());
            return redirect()->route('teams.show', $team)->with('success', 'Joined team successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
