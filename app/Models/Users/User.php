<?php

namespace App\Models\Users;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'address',
        'avatar_url',
        'is_active',
        'refresh_token_hash',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'refresh_token_hash',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Mutators and Accessors
    // Set the password attribute
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    // Get the name attribute
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Set the refresh token hash attribute
    public function setRefreshTokenHashAttribute($value)
    {
        $this->attributes['refresh_token_hash'] = bcrypt($value);
    }

    // Relationships
    // User can view a ticket
    public function canViewTicket($ticketId)
    {
        $ticket = Ticket::find($ticketId);
        return $ticket && $ticket->user_id === $this->id;
    }

    // User can access a workspace
    public function canAccessWorkspace($workspaceId)
    {
        return $this->workspaces()->where('workspace_id', $workspaceId)->exists();
    }

    // User has many workspaces
    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class, 'workspace_users')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    // User has many departments
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    // User has a department
    public function hasDepartment($departmentId)
    {
        return $this->departments()->where('department_id', $departmentId)->exists();
    }

    // User has a department role
    public function getDepartmentRole($departmentId)
    {
        $department = $this->departments()->where('department_id', $departmentId)->first();
        return $department ? $department->pivot->role : null;
    }

    /**
     * Obtiene las configuraciones de vista del usuario.
     */
    public function viewSettings()
    {
        return $this->hasMany(UserViewSetting::class);
    }

    // User has one general settings
    public function generalSettings()
    {
        return $this->hasOne(UserGeneralSetting::class);
    }

    /**
     * Obtiene la configuración de vista para un workspace específico.
     */
    public function getViewSettingForWorkspace($workspaceId)
    {
        return $this->viewSettings()->where('workspace_id', $workspaceId)->first();
    }

    /**
     * Verifica si el usuario tiene un rol específico en un workspace.
     *
     * @param int $workspaceId
     * @param array|string $roles
     * @return bool
     */
    public function hasWorkspaceRole($workspaceId, $roles)
    {
        $roles = is_array($roles) ? $roles : [$roles];
        
        return $this->workspaces()
            ->where('workspaces.id', $workspaceId)
            ->whereIn('workspace_users.role', $roles)
            ->exists();
    }
}
