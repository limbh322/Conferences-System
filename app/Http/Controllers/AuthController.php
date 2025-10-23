<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Show login page
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // important for session security
            $user = Auth::user();

            // Redirect based on role
            switch ($user->role) {
                case 'Admin':
                    return redirect()->route('dashboard')->with('success', 'Welcome Admin!');
                case 'Reviewer':
                    return redirect()->route('reviewer.home')->with('success', 'Welcome Reviewer!');
                case 'Author':
                    return redirect()->route('home')->with('success', 'Welcome Author!');
                default:
                    Auth::logout();
                    return redirect()->route('login')->withErrors(['email' => 'User role is not recognized.']);
            }
        }

        return back()->withErrors(['email' => 'Invalid email or password.']);
    }

    // Show registration page
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:Admin,Author,Reviewer',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('login')->with('success', 'Account created successfully! Please log in.');
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have logged out successfully.');
    }
}
