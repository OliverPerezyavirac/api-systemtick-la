<?php

namespace App\Listeners;

use App\Events\TicketStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\TicketStatusChangedNotification;

class SendTicketStatusChangedNotification implements ShouldQueue
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
     * @param  \App\Events\TicketStatusChanged  $event
     * @return void
     */
    public function handle(TicketStatusChanged $event)
    {
        $ticket = $event->ticket;
        $usersToNotify = collect([$ticket->creator, $ticket->assignee])->filter();

        foreach ($usersToNotify as $user) {
            $user->notify(new TicketStatusChangedNotification($event->ticket));
        }
    }
}
