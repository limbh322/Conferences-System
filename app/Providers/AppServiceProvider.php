<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       View::composer('*', function ($view) {
        if (Auth::check()) {
            $notifications = Notification::where('recipient_id', Auth::id())
                                         ->orderBy('created_at', 'desc')
                                         ->take(10) // latest 10
                                         ->get();
            $view->with('notifications', $notifications);
        }
    });
    
    }
}
