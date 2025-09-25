<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ExcludeRoles
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Nếu user có BẤT KỲ role nằm trong danh sách cấm -> chặn
        if ($user->hasAnyRole($roles)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
