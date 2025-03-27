<?php

namespace App\Models;

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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Mutators and Accessors
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function setRefreshTokenHashAttribute($value)
    {
        $this->attributes['refresh_token_hash'] = bcrypt($value);
    }

    // Relationships
    public function canViewTicket($ticketId)
    {
        $ticket = Ticket::find($ticketId);
        return $ticket && $ticket->user_id === $this->id;
    }

    public function canAccessWorkspace($workspaceId)
    {
        return $this->workspaces()->where('workspace_id', $workspaceId)->exists();
    }

    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class, 'workspace_users');
    }
}
