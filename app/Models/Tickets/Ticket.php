<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Workspaces\Workspace;
use App\Models\Users\User;
use App\Models\Departments\Department;
use App\Models\Comments\Comment;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'workspace_id',
        'creator_id',
        'assignee_id',
        'priority',
        'status',
        'category',
        'closed_at',
        'department_id',
        'assigned_to',
        'created_by',
    ];

    // Relationships
    // Ticket belongs to a workspace
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    // Ticket belongs to a creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    // Ticket belongs to an assignee
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * Obtiene el departamento al que pertenece el ticket.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Ticket has many comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
