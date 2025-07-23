<?php

namespace App\Models\Workspaces;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'logo_url',
        'owner_id',
        'department_id'
    ];

    // Relationships
    // Workspace belongs to an owner
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Workspace belongs to a department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Workspace has many users
    public function users()
    {
        return $this->belongsToMany(User::class, 'workspace_users')
                    ->withPivot('role')
                    ->withTimestamps();
    }
}
