<?php

namespace App\Models\Workspaces;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'user_id',
        'role',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    // Relationships
    // WorkspaceUser belongs to a workspace
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    // WorkspaceUser belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
