<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['workspace_id', 'name', 'color'];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_tags');
    }
}
