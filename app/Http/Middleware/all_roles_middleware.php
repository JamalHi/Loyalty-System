<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class all_roles_middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if($user->role_id != 1 && $user->role_id != 2 && $user->role_id != 3 && $user->role_id != 4 && $user->role_id != 5){
            return response()->json(['message' => 'Access Denied','status'=>403]);
        }
        return $next($request);
    }
}
