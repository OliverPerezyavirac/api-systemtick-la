<?php

namespace App\Models\Departments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Users\User;
use App\Models\Workspaces\Workspace;
use App\Models\Tickets\Ticket;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'logo_url',
        'status'
    ];

    // Relationships
    // Department has many users
    public function users()
    {
        return $this->belongsToMany(User::class, 'department_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Obtiene el workspace al que pertenece el departamento.
     */
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Obtiene los tickets asociados al departamento.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Obtiene los workspaces asociados al departamento.
     */
    public function workspaces()
    {
        return $this->hasMany(Workspace::class);
    }

    /**
     * Verifica si un usuario pertenece al departamento.
     */
    public function hasUser($userId)
    {
        return $this->users()->where('user_id', $userId)->exists();
    }

    /**
     * Obtiene el rol de un usuario en el departamento.
     */
    public function getUserRole($userId)
    {
        $user = $this->users()->where('user_id', $userId)->first();
        return $user ? $user->pivot->role : null;
    }
} 