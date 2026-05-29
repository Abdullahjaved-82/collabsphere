<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Project;
use App\Models\Task;
use App\Models\Activity;
use App\Models\User;
use App\Services\DashboardService;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
        protected ActivityService $activityService
    ) {}

    /**
     * Display the analytics dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user->load('teams');

        // If user is not in any team, render the empty team dashboard state
        if ($user->teams->isEmpty()) {
            return view('dashboard', [
                'teams' => collect([]),
                'activeTeam' => null,
                'stats' => null,
                'projectProgress' => [],
                'taskVelocity' => [],
                'memberWorkload' => [],
                'recentActivities' => collect([]),
            ]);
        }

        // Determine active team
        $teamId = $request->query('team_id');
        $activeTeam = null;

        if ($teamId) {
            $activeTeam = $user->teams()->where('team_id', $teamId)->first();
        }

        if (!$activeTeam) {
            $activeTeam = $user->teams->first();
        }

        // Auto-seed data if the active team has no projects or tasks
        $this->ensureTeamHasData($activeTeam);

        // Fetch statistics and datasets
        $stats = $this->dashboardService->getTeamStats($activeTeam);
        $projectProgress = $this->dashboardService->getProjectProgress($activeTeam);
        $taskVelocity = $this->dashboardService->getTaskVelocity($activeTeam);
        $memberWorkload = $this->dashboardService->getMemberWorkload($activeTeam);
        $recentActivities = $this->activityService->getRecentActivities($activeTeam, 10);

        return view('dashboard', [
            'teams' => $user->teams,
            'activeTeam' => $activeTeam,
            'stats' => $stats,
            'projectProgress' => $projectProgress,
            'taskVelocity' => $taskVelocity,
            'memberWorkload' => $memberWorkload,
            'recentActivities' => $recentActivities,
        ]);
    }

    /**
     * Auto-seed mock data if team contains no projects or tasks.
     */
    protected function ensureTeamHasData(Team $team): void
    {
        $projectCount = $team->projects()->count();
        if ($projectCount > 0) {
            return;
        }

        // Create 2 mock projects
        $project1 = Project::create([
            'team_id' => $team->id,
            'title' => 'Mobile Application Redesign',
            'description' => 'Overhaul the primary client mobile application to improve speed, add direct messages, and integrate the Groq AI engine.',
            'status' => 'active',
            'deadline' => Carbon::now()->addDays(30),
            'created_by' => Auth::id(),
        ]);

        $project2 = Project::create([
            'team_id' => $team->id,
            'title' => 'Cloud Infrastructure Migration',
            'description' => 'Migrate physical hardware architecture to AWS ECS and RDS database services for autoscaling capacities.',
            'status' => 'active',
            'deadline' => Carbon::now()->addDays(45),
            'created_by' => Auth::id(),
        ]);

        // Get or create a couple of mock users to assign tasks to
        $teammates = User::where('id', '!=', Auth::id())->take(2)->get();
        if ($teammates->isEmpty()) {
            // Seed a mock user if none exist
            $teammates = collect([
                User::firstOrCreate(
                    ['email' => 'teammate.sara@example.com'],
                    ['name' => 'Sara Connor', 'password' => bcrypt('password123')]
                ),
                User::firstOrCreate(
                    ['email' => 'teammate.ali@example.com'],
                    ['name' => 'Ali Raza', 'password' => bcrypt('password123')]
                )
            ]);
        }

        // Attach teammates to the team if not already done
        foreach ($teammates as $t) {
            if (!$team->users()->where('user_id', $t->id)->exists()) {
                $team->users()->attach($t->id, ['role' => 'member', 'joined_at' => Carbon::now()]);
            }
        }

        $sara = $teammates->first();
        $ali = $teammates->last();

        // --- Tasks for Project 1 (Mobile Application Redesign) ---
        $task1 = Task::create([
            'project_id' => $project1->id,
            'title' => 'Design high-fidelity UI wireframes',
            'description' => 'Create beautiful screen mockups using the primary Outfit font family and harmonized dark slate backgrounds.',
            'assigned_to' => $sara->id,
            'status' => 'done',
            'priority' => 'high',
            'position' => 0,
            'due_date' => Carbon::now()->subDays(2),
            'created_by' => Auth::id(),
            'created_at' => Carbon::now()->subDays(6),
            'updated_at' => Carbon::now()->subDays(3),
        ]);

        $task2 = Task::create([
            'project_id' => $project1->id,
            'title' => 'Establish relational database schema',
            'description' => 'Set up migrations for users, tasks, team memberships, and message read auditing logs.',
            'assigned_to' => Auth::id(),
            'status' => 'done',
            'priority' => 'medium',
            'position' => 1,
            'due_date' => Carbon::now()->subDays(5),
            'created_by' => Auth::id(),
            'created_at' => Carbon::now()->subDays(7),
            'updated_at' => Carbon::now()->subDays(5),
        ]);

        $task3 = Task::create([
            'project_id' => $project1->id,
            'title' => 'Implement direct messaging API',
            'description' => 'Create WebSocket channels or short-polling APIs supporting group messaging and announcements.',
            'assigned_to' => $ali->id,
            'status' => 'in_progress',
            'priority' => 'high',
            'position' => 0,
            'due_date' => Carbon::now()->addDays(5),
            'created_by' => Auth::id(),
            'created_at' => Carbon::now()->subDays(4),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        $task4 = Task::create([
            'project_id' => $project1->id,
            'title' => 'Conduct extensive usability testing',
            'description' => 'Run interactive testing with student test users, gathers feedback, and compile reports.',
            'assigned_to' => null,
            'status' => 'todo',
            'priority' => 'medium',
            'position' => 0,
            'due_date' => Carbon::now()->addDays(12),
            'created_by' => Auth::id(),
            'created_at' => Carbon::now()->subDays(2),
        ]);

        $task5 = Task::create([
            'project_id' => $project1->id,
            'title' => 'Write comprehensive developer API documentation',
            'description' => 'Document routes, payloads, error responses, and the Groq LLM integration parameters.',
            'assigned_to' => null,
            'status' => 'review',
            'priority' => 'low',
            'position' => 0,
            'due_date' => Carbon::now()->addDays(15),
            'created_by' => Auth::id(),
            'created_at' => Carbon::now()->subDays(1),
        ]);

        // --- Tasks for Project 2 (Cloud Infrastructure Migration) ---
        $task6 = Task::create([
            'project_id' => $project2->id,
            'title' => 'Conduct cloud competitors costs research',
            'description' => 'Compare pricing tiers, compute speeds, bandwidths, and database backup services across AWS and GCP.',
            'assigned_to' => $ali->id,
            'status' => 'done',
            'priority' => 'medium',
            'position' => 0,
            'due_date' => Carbon::now()->subDays(1),
            'created_by' => Auth::id(),
            'created_at' => Carbon::now()->subDays(3),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        $task7 = Task::create([
            'project_id' => $project2->id,
            'title' => 'Draft database migration scripts',
            'description' => 'Build safe ETL scripts transferring local SQLite data models cleanly into production RDS PostgreSQL instances.',
            'assigned_to' => Auth::id(),
            'status' => 'in_progress',
            'priority' => 'critical',
            'position' => 0,
            'due_date' => Carbon::now()->subDays(2), // Overdue!
            'created_by' => Auth::id(),
            'created_at' => Carbon::now()->subDays(4),
        ]);

        $task8 = Task::create([
            'project_id' => $project2->id,
            'title' => 'Configure GitHub actions CI/CD pipeline',
            'description' => 'Set up automated building, linting, migration running, and seamless deployment triggers to AWS ECS.',
            'assigned_to' => null,
            'status' => 'todo',
            'priority' => 'high',
            'position' => 0,
            'due_date' => Carbon::now()->addDays(10),
            'created_by' => Auth::id(),
            'created_at' => Carbon::now()->subDays(2),
        ]);

        // --- Seed Activities manually representing these actions ---
        Activity::create([
            'user_id' => Auth::id(),
            'project_id' => $project1->id,
            'task_id' => $task2->id,
            'action' => 'created',
            'description' => 'System created task \'Establish relational database schema\'',
            'created_at' => Carbon::now()->subDays(7),
        ]);

        Activity::create([
            'user_id' => Auth::id(),
            'project_id' => $project1->id,
            'task_id' => $task2->id,
            'action' => 'moved',
            'description' => Auth::user()->name . ' moved \'Establish relational database schema\' to Done',
            'created_at' => Carbon::now()->subDays(5),
        ]);

        Activity::create([
            'user_id' => $sara->id,
            'project_id' => $project1->id,
            'task_id' => $task1->id,
            'action' => 'created',
            'description' => $sara->name . ' created task \'Design high-fidelity UI wireframes\'',
            'created_at' => Carbon::now()->subDays(6),
        ]);

        Activity::create([
            'user_id' => $sara->id,
            'project_id' => $project1->id,
            'task_id' => $task1->id,
            'action' => 'moved',
            'description' => $sara->name . ' moved \'Design high-fidelity UI wireframes\' to Done',
            'created_at' => Carbon::now()->subDays(3),
        ]);

        Activity::create([
            'user_id' => $ali->id,
            'project_id' => $project2->id,
            'task_id' => $task6->id,
            'action' => 'moved',
            'description' => $ali->name . ' moved \'Conduct cloud competitors costs research\' to Done',
            'created_at' => Carbon::now()->subDays(1),
        ]);
    }
}
