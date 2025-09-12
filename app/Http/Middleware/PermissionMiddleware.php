<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class PermissionMiddleware
{
    // Dùng: ->middleware('permission:manage_products')
    public function handle($request, Closure $next, $permission)
    {
        if (!auth()->check()) return redirect()->route('login');

        if (auth()->user()->hasPermission($permission)) {
            return $next($request);
        }
        abort(403, 'Thiếu quyền: ' . $permission);
    }
}
