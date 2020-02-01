<?php

namespace App;

use App\Helpers\FailureCheck;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Laravel\Scout\Searchable;

class Merchant extends User
{
    //

    //    public function user()
    //    {
    //        return $this->morphOne('App\User', 'userable');
    //    }
    
    use Searchable;
    

    protected $fillable = [
        'id', 'name', 'address',
    ];


    public function products()
    {
        return $this->hasMany('App\Product');
    }


    public function services()
    {
        return $this->hasMany('App\Service');
    }

    public function bills()
    {
        return $this->hasMany('App\Bill');
    }

    public static function getAll()
    {


        $userMerchants = DB::table('users')
            ->select('users.id', 'users.type', 'users.username', 'users.email', 'users.image', 'merchants.name', 'merchants.address')
            ->join('merchants', 'users.id', '=', 'merchants.id')
            ->where('users.type', '=', 'Merchant')
            ->get();

        return response()->json($userMerchants, 200);
    }

    public static function showById(Request $request, Merchant $merchant)
    {
        try {
            $aMerchant = DB::table('users')
                ->select('users.id', 'users.type', 'users.username', 'users.email', 'users.image', 'merchants.name', 'merchants.address')
                ->join('merchants', 'users.id', '=', 'merchants.id')
                ->where('users.type', '=', 'Merchant')
                ->where('users.id', '=', $merchant->id);

            if ($request->input('offset') && $request->input('limit'))
                $aMerchant = $aMerchant->offset($request->input('offset'))->limit($request->input('limit'));
            $aMerchant =  $aMerchant->get();
            return response()->json($aMerchant, 200);
        } catch (\Exception $e) {
            return response()->json("Invalid query", 400);
        }
    }

    public static function getMerchantServices(Request $request, Merchant $merchant)
    {
        try {
            $services = $merchant->services();
            if ($request->input('orderBy')) {
                if ($request->input('sort'))
                    $services = $services->orderBy($request->input('orderBy'), $request->input('sort'));
                else
                    $services = $services->orderBy($request->input('orderBy'), 'asc');
            }
            if ($request->input('offset') && $request->input('limit')) {
                $services = $services->offset($request->input('offset'))->limit($request->input('limit'));
            }
            $services = $services->get();
            return response()->json($services, 200);
        } catch (\Exception $e) {
            return response()->json("Invalid query", 400);
        }
    }

    public static function getMerchantProducts(Request $request, Merchant $merchant)
    {

        try {
            $products = $merchant->products();
            if ($request->input('orderBy')) {
                if ($request->input('sort'))
                    $products = $products->orderBy($request->input('orderBy'), $request->input('sort'));
                else
                    $products = $products->orderBy($request->input('orderBy'), 'asc');
            }
            if ($request->input('offset') && $request->input('limit')) {
                $products = $products->offset($request->input('offset'))->limit($request->input('limit'));
            }
            $products = $products->get();
            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json("Invalid query", 400);
        }
    }
}
