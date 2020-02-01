<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Merchant;
use Illuminate\Http\Request;

class MerchantsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Merchant::getAll();
    }

    public function search(Request $request)
    {
        $query = $request['key']; // <-- Change the query for testing.

        $Merchants = Merchant::search($query)->get();
    
        return $Merchants;
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Merchant  $merchant
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Merchant $merchant)
    {
        return Merchant::showById($request, $merchant);
    }

    public function getMerchantServices(Request $request, Merchant $merchant)
    {
        return Merchant::getMerchantServices($request, $merchant);
    }

    public function getMerchantProducts(Request $request, Merchant $merchant)
    {
        return Merchant::getMerchantProducts($request, $merchant);
    }
}
