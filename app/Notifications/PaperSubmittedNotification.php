<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaperSubmittedNotification extends Notification
{
    use Queueable;

    public $paper;
    public $forAuthor;

    /**
     * @param \App\Models\Paper $paper
     * @param bool $forAuthor Whether this notification is for the paper author
     */
    public function __construct($paper, $forAuthor = false)
    {
        $this->paper = $paper;
        $this->forAuthor = $forAuthor;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        // Different message for author vs admin
        if ($this->forAuthor) {
            $message = 'You have successfully submitted the paper "' . $this->paper->title . '" to conference "' . $this->paper->conference->title . '".';
        } else {
            $message = 'A new paper "' . $this->paper->title . '" has been submitted by ' . $this->paper->author->name . ' for conference "' . $this->paper->conference->title . '".';
        }

        return [
            'title' => 'Paper Submission',
            'message' => $message,
            'conference_code' => $this->paper->conference->conference_code,
            'paper_id' => $this->paper->id,
        ];
    }
}
