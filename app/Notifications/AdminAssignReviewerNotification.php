<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminAssignReviewerNotification extends Notification
{
    use Queueable;

    public $paper;

    public function __construct($paper)
    {
        $this->paper = $paper;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Paper Assigned',
            'message' => 'A new paper titled "' . $this->paper->title . '" has been assigned to you for review.',
            'paper_id' => $this->paper->paper_id,
        ];
    }
}
