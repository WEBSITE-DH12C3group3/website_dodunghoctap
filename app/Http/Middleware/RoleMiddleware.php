<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    // Dùng: ->middleware('role:admin|employee')
    public function handle($request, Closure $next, $roles)
    {
        if (!auth()->check()) return redirect()->route('login');

        $userRole = optional(auth()->user()->role)->role_name;
        foreach (explode('|', $roles) as $role) {
            if ($userRole === $role) return $next($request);
        }
        abort(403, 'Bạn không có quyền truy cập.');
    }
}
