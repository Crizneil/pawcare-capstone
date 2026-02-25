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
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone' => 'required|regex:/^[0-9]{11}$/',
            'address' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'gender' => $request->gender,
            'role' => 'owner',
            'city' => 'City of Meycauayan',
            'province' => 'Bulacan',
        ]);
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'REGISTER',
            'role' => 'owner',
            'description' => 'New pet owner registered: ' . $user->name,
            'ip_address' => $request->ip(),
        ]);


        return redirect()->route('login')
            ->with('success', 'Account created successfully. Please login.');

    }
}
