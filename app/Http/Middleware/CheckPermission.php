<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        $user = auth()->user();

        if (!$user->can($permission)) {
            abort(403, 'Insufficient permissions');
        }

        return $next($request);
    }
} 