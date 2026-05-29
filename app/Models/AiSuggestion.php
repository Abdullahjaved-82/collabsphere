<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiSuggestion extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'prompt_used',
        'response_json',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'response_json' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * The project this suggestion belongs to.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}

