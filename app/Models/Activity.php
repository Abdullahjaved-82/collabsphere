<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;

    // We only have created_at column
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'project_id',
        'task_id',
        'action',
        'description',
    ];

    /**
     * The user who triggered this activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The project this activity belongs to.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * The task associated with this activity.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
