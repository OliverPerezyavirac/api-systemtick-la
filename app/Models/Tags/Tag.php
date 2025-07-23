<?php

namespace App\Models\Tags;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['workspace_id', 'name', 'color'];

    // Relationships
    // Tag belongs to a workspace
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    // Tag has many tickets
    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_tags');
    }
}
