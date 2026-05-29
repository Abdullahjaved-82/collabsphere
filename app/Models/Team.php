<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'invite_code',
        'created_by',
    ];

    /**
     * Boot the model — auto-generate invite_code.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Team $team) {
            if (empty($team->invite_code)) {
                $team->invite_code = strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Users belonging to this team.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members')
            ->withPivot('role', 'joined_at');
    }

    /**
     * Projects under this team.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * The user who created this team.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Announcements posted to this team.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
