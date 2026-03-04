<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pet;
use App\Models\Appointment;
use App\Models\ActivityLog;


class AdminAuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            $role = Auth::user()->role;
            return match ($role) {
                'admin' => redirect()->route('admin.dashboard'),
                'staff' => redirect()->route('staff.dashboard'),
                'owner' => redirect()->route('pet-owner.dashboard'),
                'user' => redirect()->route('pet-owner.dashboard'),
                default => redirect('/'),
            };
        }
        return view('login');
    }

    /**
     * Handle login request (REPLACED VERSION)
     */
    public function login(Request $request)
    {
        // 1. Validate the input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Attempt to log in using database credentials
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            ActivityLog::record(
                'LOGIN',
                'User logged in successfully'
            );
            // 3. Check the role from the 'users' table
            $role = Auth::user()->role;

            // 4. Redirect based on that role
            return match ($role) {
                'admin' => redirect()->route('admin.dashboard'),
                'staff' => redirect()->route('staff.dashboard'),
                'owner' => redirect()->route('pet-owner.dashboard'),
                default => redirect('/'),
            };
        }

        ActivityLog::create([
            'user_id' => null,
            'action' => 'FAILED_LOGIN',
            'role' => 'guest',
            'description' => 'Failed login attempt for email: ' . $request->email,
            'ip_address' => $request->ip(),
        ]);

        // 5. If login fails, return with error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        ActivityLog::record(
            'LOGOUT',
            'User logged out'
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }
}
