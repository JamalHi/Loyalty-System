<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class access_controller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        $user_role = Role::find($user->role_id);

        $permissionName = $request->route()->getName();

        if($user->active != 1)
        {
            return response()->json(['message' => 'Access Denied, you need to reactivate your account','status'=>403]);
        }

        if(! $user_role->check($permissionName)){
            return response()->json(['message' => 'Access Denied','status'=>403]);
        }
        return $next($request);
    }
}
