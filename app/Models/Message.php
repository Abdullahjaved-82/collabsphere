<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'team_id',
        'type',
        'subject',
        'body',
        'is_pinned',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'is_pinned' => 'boolean',
    ];

    /**
     * The user who sent this message
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * The user who receives this direct message
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * The team this announcement belongs to
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Users who have read this message (for announcements)
     */
    public function reads(): HasMany
    {
        return $this->hasMany(MessageRead::class);
    }

    /**
     * Scope: only direct messages
     */
    public function scopeDirect($query)
    {
        return $query->where('type', 'direct');
    }

    /**
     * Scope: only announcements
     */
    public function scopeAnnouncements($query)
    {
        return $query->where('type', 'announcement');
    }

    /**
     * Check if a user has read this message
     */
    public function isReadBy(User $user): bool
    {
        if ($this->type === 'direct') {
            return $this->read_at !== null;
        }

        return $this->reads()->where('user_id', $user->id)->exists();
    }
}
