<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionLimit
{
    public function handle(Request $request, Closure $next, string $feature = ''): Response
    {
        // TODO: implement subscription limit checks per feature
        return $next($request);
    }
}
