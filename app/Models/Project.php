<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'title',
        'description',
        'status',
        'deadline',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
        ];
    }

    /**
     * The team this project belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Tasks within this project.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * AI suggestion for this project.
     */
    public function aiSuggestion(): HasOne
    {
        return $this->hasOne(AiSuggestion::class);
    }

    /**
     * The user who created this project.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
