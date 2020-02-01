<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;

class ownService
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

        $service = $request->route('service');

        if($service->merchant_id !== auth()->id())
        {
            return response()->json(ApiResponse::error("Access Denied"), 403);
        }


        return $next($request);
    }
}
