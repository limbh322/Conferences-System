<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewerSubmitReviewNotification extends Notification
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
            'title' => 'Your Paper Has Been Reviewed',
            'message' => 'Your paper "' . $this->paper->title . '" has been reviewed. Check the feedback and status.',
            'paper_id' => $this->paper->paper_id,
        ];
    }
}
