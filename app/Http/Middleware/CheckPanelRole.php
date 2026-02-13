<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPanelRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has the required role
        $hasRole = match($role) {
            'admin' => $user->isAdmin(),
            'staff' => $user->isStaff(),
            default => false,
        };

        if (!$hasRole) {
            // User doesn't have the required role
            // Redirect based on their actual role
            if ($user->isAdmin()) {
                return redirect('/admin');
            }
            
            if ($user->isStaff()) {
                return redirect('/staff');
            }
            
            if ($user->isStudent()) {
                return redirect()->route('onboarding');
            }

            // Default fallback
            return redirect('/')->with('error', __('You do not have permission to access this panel.'));
        }

        return $next($request);
    }
}