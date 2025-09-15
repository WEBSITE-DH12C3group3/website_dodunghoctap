<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class RoleMiddleware
{
    public function handle($request, Closure $next, $roles)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $allowed = collect(explode('|', $roles))
            ->map(fn($r) => Str::lower(trim($r)))
            ->contains(fn($r) => $user->hasRole($r));

        if (!$allowed) {
            return redirect()->route('store.index')
                ->with('error', 'Bạn không có quyền truy cập khu vực này.');
        }
        return $next($request);
    }
}
