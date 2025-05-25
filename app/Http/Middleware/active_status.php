<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class active_status
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if($request->header('Authorization') != null){
            $user = auth()->user();
        }
        else{
            if($request->email != null){
                $user = User::where('email',$request->email)->first();
                if(is_Null($user))
                {
                    return response()->json([$user,'status'=>404 ,'message' => 'not found'] , status:404);
                }
            }
        }

        if($user->active != 1)
        {
            return response()->json(['message' => 'Access Denied, you need to activate your account','status'=>402] , status:402);
        }
        return $next($request);
    }
}
