<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    use ApiResponse;

    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasAnyRole(explode(',', $roles))) {
            return $this->error('Unauthorized: role not permitted.', null, 403);
        }

        return $next($request);
    }
}
