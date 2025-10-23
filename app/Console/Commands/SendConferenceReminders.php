<?php
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Conference;
use App\Notifications\ConferenceReminderNotification;
use Carbon\Carbon;

class SendConferenceReminders extends Command
{
    protected $signature = 'reminders:conference';
    protected $description = 'Send reminders for upcoming conference deadlines';

    public function handle()
    {
        $upcomingConferences = Conference::whereDate('deadline', '<=', Carbon::now()->addWeek())
                                         ->get();

        $authors = User::where('role', 'Author')->get();

        foreach ($upcomingConferences as $conference) {
            foreach ($authors as $author) {
                $author->notify(new ConferenceReminderNotification($conference));
            }
        }

        $this->info('Conference reminders sent successfully!');
    }
}
