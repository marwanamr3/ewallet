<?php

namespace App;

use App\Helpers\ApiResponse;
use Illuminate\Database\Eloquent\Model;
use mysql_xdevapi\Exception;

class Bill extends Model
{
    public function Transaction()
   {
       return $this->morphOne('App\Transaction', 'transactionable');
   }


    public function merchant()
    {
        return $this->belongsTo('App\Merchant');
    }

    public function billable()
    {
        return $this->morphTo();
    }

    static function createProductBill(Product $product, User $user)
    {
        if(!$product || !$user)
        {
            return  response()->json(ApiResponse::error("Error retrieving product"), 400);
        }

        $reference = uniqid();

//        dd('#'.$reference);

        try{
            return Transaction::createProductBill($product, $reference);
        }
        catch (\Exception $e)
        {
            throw $e;
        }



//        if (Transaction::createProductBill($product, $reference)) {
////            dd('transaction');
//            return  response()->json(["message" => "Success"], 200);
//        } else {
//            return  response()->json(ApiResponse::error("Error Creating Bill"), 400);
//        }
    }
}
