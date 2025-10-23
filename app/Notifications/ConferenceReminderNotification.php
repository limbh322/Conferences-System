<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConferenceReminderNotification extends Notification
{
    use Queueable;

    public $conference;

    public function __construct($conference)
    {
        $this->conference = $conference;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Conference Deadline Reminder',
            'message' => 'The conference "' . $this->conference->title . '" is nearing its deadline (' . $this->conference->deadline . '). Don’t forget to submit your paper!',
            'conference_code' => $this->conference->conference_code,
        ];
    }
}
