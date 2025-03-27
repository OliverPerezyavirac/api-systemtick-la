<?php

namespace App\Listeners;

use App\Events\WorkspaceAdded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\WorkspaceAddedNotification;

class SendWorkspaceAddedNotification implements ShouldQueue
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
     * @param  \App\Events\WorkspaceAdded  $event
     * @return void
     */
    public function handle(WorkspaceAdded $event)
    {
        $event->workspaceUser->user->notify(new WorkspaceAddedNotification($event->workspaceUser));
    }
}
