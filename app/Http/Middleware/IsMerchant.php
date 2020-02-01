<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;

class IsMerchant
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
        $loggedInUser = auth()->user();
        if($loggedInUser->type === 'Customer'){
            return response()->json(ApiResponse::error("Access Denied"), 403);
        }
        return $next($request);
    }
}
