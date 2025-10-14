<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AccountantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        if (!auth()->user()->isAccountant() && !auth()->user()->isAdmin()) {
            abort(403, 'Only accountants and admins can access this section.');
        }

        return $next($request);
    }
}