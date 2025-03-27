<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user || !$user->isOneOf($roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
