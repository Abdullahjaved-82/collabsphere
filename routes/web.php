<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // Onboarding Wizard Routes (Must be accessible while onboarding is not completed)
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding/step1', [OnboardingController::class, 'step1'])->name('onboarding.step1');
    Route::post('/onboarding/step2', [OnboardingController::class, 'step2'])->name('onboarding.step2');
    Route::post('/onboarding/step3', [OnboardingController::class, 'step3'])->name('onboarding.step3');
    Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');
    
    // API Notifications
    Route::get('/api/notifications', function () {
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'type' => $notif->type,
                    'data' => $notif->data,
                    'read_at' => $notif->read_at,
                    'time_ago' => $notif->created_at->diffForHumans(),
                ];
            });
        
        $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count();
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    })->name('api.notifications');

    Route::post('/api/notifications/mark-all-read', function () {
        auth()->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    })->name('api.notifications.markAllRead');

    // Global Search index API
    Route::get('/api/search', function (\Illuminate\Http\Request $request) {
        $searchTerm = $request->query('q', '');
        
        if (strlen($searchTerm) < 2) {
            return response()->json(['results' => []]);
        }
        
        $user = auth()->user();
        $teamIds = $user->teams()->pluck('teams.id')->toArray();
        
        // 1. Projects Search
        $projects = \App\Models\Project::whereIn('team_id', $teamIds)
            ->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            })
            ->with('team')
            ->take(5)
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'subtitle' => $project->team ? $project->team->name : 'No Team',
                    'url' => route('projects.show', $project),
                    'icon' => '📁'
                ];
            });
            
        // 2. Tasks Search
        $projectIds = \App\Models\Project::whereIn('team_id', $teamIds)->pluck('id')->toArray();
        $tasks = \App\Models\Task::whereIn('project_id', $projectIds)
            ->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            })
            ->with('project')
            ->take(5)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'subtitle' => $task->project ? 'In Project: ' . $task->project->title : 'No Project',
                    'url' => route('tasks.show', $task),
                    'icon' => '📋'
                ];
            });
            
        // 3. Team Members Search
        $members = \App\Models\User::whereIn('id', function($query) use ($teamIds) {
            $query->select('user_id')->from('team_members')->whereIn('team_id', $teamIds);
        })
        ->where('id', '!=', $user->id)
        ->where(function($q) use ($searchTerm) {
            $q->where('name', 'like', "%{$searchTerm}%")
              ->orWhere('email', 'like', "%{$searchTerm}%");
        })
        ->take(5)
        ->get()
        ->map(function ($member) {
            return [
                'id' => $member->id,
                'title' => $member->name,
                'subtitle' => $member->email,
                'url' => route('teams.index'),
                'icon' => '👤'
            ];
        });
        
        $results = [];
        if ($projects->isNotEmpty()) $results['Projects'] = $projects;
        if ($tasks->isNotEmpty()) $results['Tasks'] = $tasks;
        if ($members->isNotEmpty()) $results['Team Members'] = $members;
        
        return response()->json(['results' => $results]);
    })->name('api.search');
});

// Authenticated and fully onboarded routes
Route::middleware(['auth', 'onboarded', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'onboarded'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('teams', TeamController::class);
    Route::post('/teams/join', [TeamController::class, 'join'])->name('teams.join');

    Route::resource('projects', ProjectController::class);

    Route::get('/projects/{project}/kanban', [KanbanController::class, 'show'])->name('projects.kanban');
    Route::get('/projects/{project}/kanban-data', [KanbanController::class, 'getData'])->name('projects.kanbanData');
    Route::patch('/tasks/{task}/position', [KanbanController::class, 'updatePosition'])->name('tasks.updatePosition');
    Route::post('/projects/{project}/tasks-ajax', [KanbanController::class, 'store'])->name('tasks.storeAjax');

    Route::get('/my-tasks', function () {
        return view('projects.my-tasks');
    })->name('tasks.index');

    Route::get('/projects/{project}/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::post('/tasks/{task}/claim', [TaskController::class, 'claim'])->name('tasks.claim');
    Route::post('/tasks/{task}/approve-claim', [TaskController::class, 'approveClaim'])->name('tasks.approveClaim');

    // Message Routes
    Route::get('/messages/inbox', [MessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/sent', [MessageController::class, 'sent'])->name('messages.sent');
    Route::get('/messages/compose', [MessageController::class, 'compose'])->name('messages.compose');
    Route::post('/messages/compose', [MessageController::class, 'compose'])->name('messages.send');
    Route::get('/messages/announcement', [MessageController::class, 'announcement'])->name('messages.announcement');
    Route::post('/messages/announcement', [MessageController::class, 'announcement'])->name('messages.sendAnnouncement');
    Route::get('/messages/announcements', [MessageController::class, 'announcements'])->name('messages.announcements');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::post('/messages/{message}/pin', [MessageController::class, 'pin'])->name('messages.pin');
    Route::post('/messages/mark-all-read', [MessageController::class, 'markAllRead'])->name('messages.markAllRead');
    Route::get('/api/messages/unread-count', [MessageController::class, 'getUnreadCount'])->name('api.messages.unreadCount');

    // AI Assistant Routes
    Route::get('/projects/{project}/ai-assistant', [AIController::class, 'index'])->name('projects.ai');
    Route::post('/projects/{project}/ai/generate', [AIController::class, 'generateBreakdown'])->name('projects.ai.generate');
    Route::post('/projects/{project}/ai/accept', [AIController::class, 'acceptSuggestions'])->name('projects.ai.accept');
    Route::post('/ai/suggestions/{suggestion}/reject', [AIController::class, 'rejectSuggestion'])->name('ai.suggestions.reject');
});

require __DIR__.'/auth.php';
