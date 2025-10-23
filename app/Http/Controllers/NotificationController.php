<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // List all notifications for current user
    public function index()
    {
        $notifications = Notification::where('recipient_id', Auth::id())
                                     ->orderBy('created_at', 'desc')
                                     ->get();

        return view('notifications.index', compact('notifications'));
    }

    // Mark a notification as read
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
                                    ->where('recipient_id', Auth::id())
                                    ->firstOrFail();

        $notification->update(['read_at' => now()]);

        return redirect()->back();
    }

    // Get unread notification count
    public function unreadCount()
    {
        return Notification::where('recipient_id', Auth::id())
                           ->whereNull('read_at')
                           ->count();
 
                        }
                        public function destroy($id)
{
    $notification = Notification::findOrFail($id);
    if ($notification->recipient_id !== auth()->id()) {
        abort(403);
    }
    $notification->delete();

    return back()->with('success', 'Notification deleted successfully.');
}

}
