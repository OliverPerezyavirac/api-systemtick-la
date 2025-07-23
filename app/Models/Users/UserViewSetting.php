<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserViewSetting extends Model
{
    use HasFactory;

    // Relationships
    // UserViewSetting belongs to a user
    protected $fillable = [
        'user_id',
        'workspace_id',
        'view_type',
        'additional_settings'
    ];

    protected $casts = [
        'additional_settings' => 'array'
    ];

    /**
     * Obtiene el usuario al que pertenece esta configuración.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el workspace al que pertenece esta configuración.
     */
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
} 
