<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Admin gaat naar admin panel
            if ($user->hasRole('admin')) {
                return redirect('/admin');
            }
            
            // Organizer gaat naar organizer dashboard
            if ($user->hasRole('organizer')) {
                return redirect('/organizer');
            }
            
            // Regular users blijven op de main dashboard
            return $next($request);
        }

        return $next($request);
    }
}
