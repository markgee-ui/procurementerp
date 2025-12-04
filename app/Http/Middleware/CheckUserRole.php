<?php

// app/Http/Middleware/CheckUserRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string ...$roles  The list of allowed roles (e.g., 'procurement', 'qs')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Check if the user is authenticated at all
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. Check if the user's role is in the list of allowed roles
        // We use in_array to support multiple roles for a single route group if needed.
        if (! in_array($user->role, $roles)) {
            // Log the unauthorized attempt
            \Log::warning("Unauthorized access attempt by user {$user->id} ({$user->email}) to route: {$request->path()}");
            
            // Deny access
            return abort(403, 'Access Denied. You do not have the required role for this section.');
        }

        return $next($request);
    }
}
