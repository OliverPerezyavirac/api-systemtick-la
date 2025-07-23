<?php

namespace App\Models\Workspaces;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'invited_by',
        'email',
        'role',
        'status',
    ];

    // Relationships
    // WorkspaceInvitation belongs to a workspace
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    // WorkspaceInvitation belongs to an inviter
    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
