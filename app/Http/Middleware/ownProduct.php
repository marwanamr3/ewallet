<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use App\Merchant;
use App\Product;
use Closure;

class ownProduct
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
        $product = $request->route('product');

        if($product->merchant_id !== auth()->id())
        {
            return response()->json(ApiResponse::error("Access Denied"), 403);
        }


        return $next($request);
    }
}
