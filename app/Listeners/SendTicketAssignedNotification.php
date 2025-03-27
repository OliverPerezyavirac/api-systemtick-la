<?php

namespace App\Listeners;

use App\Events\TicketAssigned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\TicketAssignedNotification;

class SendTicketAssignedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\TicketAssigned  $event
     * @return void
     */
    public function handle(TicketAssigned $event)
    {
        $event->ticket->assignee->notify(new TicketAssignedNotification($event->ticket));
    }
}
