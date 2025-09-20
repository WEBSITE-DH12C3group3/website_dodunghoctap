<?php

  namespace App\Http\Middleware;

  use Closure;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Facades\Log;

  class UpdateLastActivity
  {
      public function handle(Request $request, Closure $next)
      {
          if (Auth::check()) {
              $user = Auth::user();
              try {
                  $user->last_activity = now();
                  $user->save();
                  Log::info('Updated last_activity for user: ' . $user->id);
              } catch (\Exception $e) {
                  Log::error('Failed to update last_activity for user ' . $user->id . ': ' . $e->getMessage());
              }
          }

          return $next($request);
      }
  }