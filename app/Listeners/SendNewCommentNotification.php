<?php

namespace App\Listeners;

use App\Events\NewComment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\NewCommentNotification;

class SendNewCommentNotification implements ShouldQueue
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
     * @param  \App\Events\NewComment  $event
     * @return void
     */
    public function handle(NewComment $event)
    {
        $ticket = $event->comment->ticket;
        $usersToNotify = collect([$ticket->creator, $ticket->assignee])->filter();

        foreach ($usersToNotify as $user) {
            $user->notify(new NewCommentNotification($event->comment));
        }
    }
}
