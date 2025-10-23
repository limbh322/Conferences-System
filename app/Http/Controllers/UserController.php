<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;
use App\Models\Conference;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Show registration form (for normal users)
     */
    public function register()
    {
        return view('auth.register'); 
    }

    /**
     * Handle user registration (for normal users or admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'nullable|in:Admin,Author,Reviewer',
        ]);

        $role = $request->role ?? 'Author';

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $role,
        ]);

        if (auth()->check() && auth()->user()->role === 'Admin') {
            return redirect()->back()->with('success', '✅ User created successfully!');
        }

        return redirect()->route('login')->with('success', '🎉 Registration successful! You can now log in.');
    }

    /**
     * Admin: show create user form
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Admin: view all users
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Admin: assign role to a user
     */
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:Admin,Author,Reviewer',
        ]);

        $user->role = $request->role;
        $user->save();

        return redirect()->back()->with('success', "Role updated for {$user->name}!");
    }

    /**
     * Admin Dashboard with notifications
     */
    public function dashboard()
    {
        // Fetch all conferences
        $conferences = Conference::all();

        // Fetch latest notifications for logged-in admin
        $notifications = Notification::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->take(10)
                            ->get();

        return view('admin.dashboard', compact('conferences', 'notifications'));
    }
}
