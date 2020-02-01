<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;

class IsCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        $loggedInUser = Auth::guard('api')->user();
        $loggedInUser = auth()->user();
        if($loggedInUser->type === 'Merchant'){
//            dd($loggedInUser);
            return response()->json(ApiResponse::error("Access Denied"), 403);
        }
        return $next($request);
    }
}
