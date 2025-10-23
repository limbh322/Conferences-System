<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Conference;
use App\Models\User;
use App\Notifications\ConferenceReminderNotification;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            // Get conferences whose deadline is within the next 3 days
            $conferences = Conference::whereDate('deadline', '<=', Carbon::now()->addDays(3))->get();
            $authors = User::where('role', 'Author')->get();

            foreach ($conferences as $conf) {
                foreach ($authors as $author) {
                    $author->notify(new ConferenceReminderNotification($conf));
                }
            }
        })->daily(); // run once daily
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
