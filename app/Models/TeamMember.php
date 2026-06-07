<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    protected $fillable = [
        'name', 'role', 'bio', 'credentials', 'image', 'sort_order',
        'is_active', 'department_id', 'is_consultant',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_consultant' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
