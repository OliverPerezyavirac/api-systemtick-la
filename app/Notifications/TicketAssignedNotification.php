<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Tickets\Ticket;
use App\Models\Users\User;


class TicketAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $ticket;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {

        // Get the assignee user
        return [
            'ticket_id' => $this->ticket->id,
            'assignee_id' => $this->ticket->assignee_id,
            'ticket_title' => $this->ticket->title,
            'workspace_id' => $this->ticket->workspace_id,
            'workspace_name' => $this->ticket->workspace->name,
            'message' => "Se te ha asignado el ticket '{$this->ticket->title}' en el workspace '{$this->ticket->workspace->name}'"
        ];
    }
}
