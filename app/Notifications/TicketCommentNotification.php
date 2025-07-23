<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use App\Models\Tickets\Ticket;
use App\Models\Comments\Comment;

class TicketCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $comment;
    protected $commenter;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, Comment $comment, $commenter)
    {
        $this->ticket = $ticket;
        $this->comment = $comment;
        $this->commenter = $commenter;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ticket_comment',
            'ticket_id' => $this->ticket->id,
            'ticket_title' => $this->ticket->title,
            'workspace_id' => $this->ticket->workspace_id,
            'comment_id' => $this->comment->id,
            'comment_content' => $this->comment->content,
            'commenter' => [
                'id' => $this->commenter->id,
                'name' => $this->commenter->first_name . ' ' . $this->commenter->last_name
            ],
            'message' => "{$this->commenter->first_name} {$this->commenter->last_name} ha comentado en el ticket '{$this->ticket->title}'"
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {

        // Get the assignee user
        return new BroadcastMessage([
            'type' => 'ticket_comment',
            'ticket_id' => $this->ticket->id,
            'ticket_title' => $this->ticket->title,
            'workspace_id' => $this->ticket->workspace_id,
            'comment_id' => $this->comment->id,
            'comment_content' => $this->comment->content,
            'commenter' => [
                'id' => $this->commenter->id,
                'name' => $this->commenter->first_name . ' ' . $this->commenter->last_name
            ],
            'message' => "{$this->commenter->first_name} {$this->commenter->last_name} ha comentado en el ticket '{$this->ticket->title}'"
        ]);
    }
}
