<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $rolecheck): Response
    {
        $user = $request->user();
        $level = DB::table('tb_user_levels')->where('level_code', $rolecheck)->first();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        if ($level->user_level_id !== $user->user_level_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return $next($request);
    }
}
