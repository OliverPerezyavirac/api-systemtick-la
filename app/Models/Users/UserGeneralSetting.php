<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sidebar_expanded',
        'additional_settings'
    ];

    protected $casts = [
        'additional_settings' => 'array',
        'sidebar_expanded' => 'boolean'
    ];

    // Relationships
    // UserGeneralSetting belongs to a user
    /**
     * Obtiene el usuario al que pertenece esta configuración.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 