<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TeamMember extends Pivot
{
    protected $table = 'team_members';

    public $timestamps = false;

    protected $fillable = [
        'team_id',
        'user_id',
        'role',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
        ];
    }
}
