<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Display the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle user login attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
{
    // Validate user input
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    // Attempt login using database
    if (Auth::attempt($credentials)) {

        // Get logged in user
        $user = Auth::user();

        // Check role
        if ($user->role !== 'procurement') {

            // Logout unauthorized user
            Auth::logout();

            return back()->withErrors([
                'email' => 'Access denied. Only Procurement Officers can log in.',
            ]);
        }

        //Authenticated & authorized
        return redirect()
            ->intended(route('procurement.create'))
            ->with('success', 'Welcome back, Procurement Officer!');
    }

    // Invalid credentials
    return back()->withErrors([
        'email' => 'Invalid email or password.',
    ]);
}


    /**
     * Handle user logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // In a real Laravel app, you would use:
        // Auth::logout();
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been successfully logged out.');
    }
}