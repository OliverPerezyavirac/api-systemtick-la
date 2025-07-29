<?php

namespace App\Models\Comments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tickets\Ticket;
use App\Models\Users\User;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'content',
        'visibility',
    ];

    // Relationships
    // Comment belongs to a ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Comment belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
