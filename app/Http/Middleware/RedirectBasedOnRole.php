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
            
            if ($user->hasRole('admin')) {
                return redirect()->route('filament.admin.pages.dashboard');
            }
            
            if ($user->hasRole('organizer')) {
                return redirect()->route('filament.organizer.pages.dashboard');
            }
            
            // Default voor users
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
