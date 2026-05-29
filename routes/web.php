<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AIController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
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
