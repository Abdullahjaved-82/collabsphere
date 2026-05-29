<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Project;
use App\Services\TeamService;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OnboardingController extends Controller
{
    public function __construct(
        protected TeamService $teamService,
        protected ProjectService $projectService
    ) {}

    /**
     * Show the onboarding wizard page.
     */
    public function show()
    {
        $user = Auth::user();
        if ($user->has_completed_onboarding) {
            return redirect()->route('dashboard');
        }

        // Fetch joinable teams if any (or user's teams)
        $teams = Team::latest()->take(5)->get();

        return view('projects.onboarding', compact('user', 'teams'));
    }

    /**
     * Step 1: Update Bio and Upload Avatar
     */
    public function step1(Request $request)
    {
        $validated = $request->validate([
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048', // max 2MB
        ]);

        $user = Auth::user();
        $user->bio = $validated['bio'] ?? $user->bio;

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : null
        ]);
    }

    /**
     * Step 2: Create or Join Team
     */
    public function step2(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:create,join',
            'name' => 'required_if:action,create|string|max:255',
            'description' => 'nullable|string|max:1000',
            'invite_code' => 'required_if:action,join|string|size:8',
        ]);

        $user = Auth::user();

        try {
            if ($validated['action'] === 'create') {
                $team = $this->teamService->createTeam([
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? '',
                ], $user);

                return response()->json([
                    'success' => true,
                    'message' => 'Team created successfully!',
                    'team' => $team
                ]);
            } else {
                $team = $this->teamService->joinTeam($validated['invite_code'], $user);

                return response()->json([
                    'success' => true,
                    'message' => 'Joined team successfully!',
                    'team' => $team
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Step 3: Create First Project
     */
    public function step3(Request $request)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'deadline' => 'nullable|date',
        ]);

        $user = Auth::user();
        $team = Team::findOrFail($validated['team_id']);

        try {
            $project = $this->projectService->createProject([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? '',
                'status' => 'active',
                'deadline' => $validated['deadline'] ?? null,
            ], $team, $user);

            return response()->json([
                'success' => true,
                'message' => 'First project created successfully!',
                'project' => $project
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Complete Onboarding
     */
    public function complete()
    {
        $user = Auth::user();
        $user->has_completed_onboarding = true;
        $user->save();

        session()->flash('success', 'Onboarding completed! Welcome to CollabSphere.');

        return response()->json([
            'success' => true,
            'redirect' => route('dashboard')
        ]);
    }
}
