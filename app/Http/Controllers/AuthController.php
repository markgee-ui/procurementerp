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
   // app/Http/Controllers/AuthController.php

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
        
        //  NEW: Define allowed roles
        $allowedRoles = ['procurement', 'qs','pm','offpm']; 

        // Check role authorization
        if (!in_array($user->role, $allowedRoles)) {

            // Logout unauthorized user
            Auth::logout();

            return back()->withErrors([
                'email' => 'Access denied. Your user role is not authorized to use this system.',
            ]);
        }

        // Authenticated & authorized. Determine correct redirect route based on role.
        if ($user->role === 'procurement') {
            $intendedRoute = route('procurement.create');
            $message = 'Welcome back, Procurement Officer!';
        } elseif ($user->role === 'qs') {
            // Send QS Officers to the QS Index
            $intendedRoute = route('qs.index');
            $message = 'Welcome back, Quality & Standards Officer!';
        } elseif ($user->role === 'pm') {
            $intendedRoute = route('pm.index'); 
            $message = 'Welcome back, Project Manager!';
        } elseif ($user->role === 'offpm') {
            $intendedRoute = route('opm.index'); 
            $message = 'Welcome back, Project Manager!';
        } else {
            // Fallback for an authorized but unexpected role
            $intendedRoute = route('login'); 
            $message = 'Welcome back!';
        }

        return redirect()
            ->intended($intendedRoute)
            ->with('success', $message);
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