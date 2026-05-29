<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Repositories\Eloquent\ProjectRepository;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\Eloquent\TaskRepository;
use App\Repositories\Interfaces\TeamRepositoryInterface;
use App\Repositories\Eloquent\TeamRepository;
use App\Repositories\Interfaces\MessageRepositoryInterface;
use App\Repositories\Eloquent\MessageRepository;
use Illuminate\Support\Facades\Gate;
use App\Models\Message;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(TeamRepositoryInterface::class, TeamRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define gates for message authorization
        Gate::define('view-message', function ($user, Message $message) {
            if ($message->type === 'direct') {
                return $user->id === $message->sender_id || $user->id === $message->receiver_id;
            }
            return $user->teams()->where('team_id', $message->team_id)->exists();
        });

        Gate::define('delete-message', function ($user, Message $message) {
            if ($user->id === $message->sender_id) {
                return true;
            }
            if ($message->type === 'announcement') {
                return $user->teams()
                    ->where('team_id', $message->team_id)
                    ->wherePivot('role', 'leader')
                    ->exists();
            }
            return false;
        });

        Gate::define('pin-message', function ($user, Message $message) {
            if ($message->type === 'announcement') {
                return $user->teams()
                    ->where('team_id', $message->team_id)
                    ->wherePivot('role', 'leader')
                    ->exists();
            }
            return false;
        });

        Gate::define('sendAnnouncement', function ($user, $teamId) {
            return $user->teams()
                ->where('team_id', $teamId)
                ->wherePivot('role', 'leader')
                ->exists();
        });
    }
}
