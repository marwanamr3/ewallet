<?php

namespace App;

use App\Helpers\ApiResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;

class ProductBill extends Model
{
    public function Bill()
    {
        return $this->morphOne('App\Bill', 'billable');
    }

    protected $fillable = [
        'id',
        'product_id',
        'created_at',
        'updated_at',
    ];

    static function createProductBill(Product $product)
    {
        if(!$product)
        {
            return  response()->json(ApiResponse::error("Error retrieving product"), 400);
        }

        $user = auth()->user();

//        $product_bill = new ProductBill();
//        $product_bill->product_id = $product->id;
//        $product_bill->save();

//        return $product->id;

        try{
            return Bill::createProductBill($product, $user);
        }
        catch (\Exception $e)
        {
            throw $e;
        }

//        if (Bill::createProductBill($product, $user)) {
////            dd('transaction');
//            return  response()->json(["message" => "Success"], 200);
//        } else {
//            return  response()->json(ApiResponse::error("Error Creating Bill"), 400);
//        }

//        return $product;
    }
}
