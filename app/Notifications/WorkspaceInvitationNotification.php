<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use App\Models\Workspaces\Workspace;

class WorkspaceInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $workspace;
    protected $role;
    protected $invitedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Workspace $workspace, string $role, $invitedBy)
    {
        $this->workspace = $workspace;
        $this->role = $role;
        $this->invitedBy = $invitedBy;
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
        // Get the invited user
        return [
            'type' => 'workspace_invitation',
            'workspace_id' => $this->workspace->id,
            'workspace_name' => $this->workspace->name,
            'role' => $this->role,
            'invited_by' => [
                'id' => $this->invitedBy->id,
                'name' => $this->invitedBy->first_name . ' ' . $this->invitedBy->last_name
            ],
            'message' => "Has sido agregado al workspace '{$this->workspace->name}' como {$this->role} por {$this->invitedBy->first_name} {$this->invitedBy->last_name}"
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        // Get the invited user
        return new BroadcastMessage([
            'type' => 'workspace_invitation',
            'workspace_id' => $this->workspace->id,
            'workspace_name' => $this->workspace->name,
            'role' => $this->role,
            'invited_by' => [
                'id' => $this->invitedBy->id,
                'name' => $this->invitedBy->first_name . ' ' . $this->invitedBy->last_name
            ],
            'message' => "Has sido agregado al workspace '{$this->workspace->name}' como {$this->role} por {$this->invitedBy->first_name} {$this->invitedBy->last_name}"
        ]);
    }
}
