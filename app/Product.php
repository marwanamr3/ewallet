<?php

namespace App;

use App\Helpers\FailureCheck;
use App\Helpers\ApiResponse;
use Illuminate\Database\Eloquent\Model;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Laravel\Scout\Searchable;

class Product extends Model
{
    //
    use searchable;
    
    protected $fillable = [
        'merchant_id',
        'name',
        'description',
        'image'
    ];


    protected $casts = [
        'price' => 'float',
        'period' => 'int'
    ];


    public function merchant()
    {
        return $this->belongsTo('App\Merchant');
    }

    public function productBill()
    {
        return $this->hasOne('App\ProductBill', 'product_id');
    }


    static function createProduct(Request $request)
    {

        //Validate request input
        $rules = [
            'name' => 'required|min:4|string',
            'description' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return  response()->json(ApiResponse::error($validator->errors()), 400);
        }

        $request->merge(array("merchant_id" => auth()->id()));

        if (Product::create($request->all()))
            return response()->json(ApiResponse::success("Product Created Successfully"), 201);

        return response()->json(ApiResponse::error(["data" => "Product Creation Failed"]), 400);
    }

    static function showById(Product $product)
    {
        return response()->json($product, 200);
    }

    static function updateById(Request $request, Product $product)
    {
        $rules = [
            'name' => 'sometimes|min:4|string',
            'description' => 'sometimes|string',
            'image' => 'sometimes|mimes:jpeg,bmp,png'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return  response()->json(ApiResponse::error($validator->errors()), 400);
        }


        try {
            DB::beginTransaction();

            // update image - if given (Postman: uses form data not form-urlencoded)
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('images'); // Folder path is  e-wallet/storage/app/images
                $product->image = explode('/', $path)[1];
            }

            $product->update($request->only(['name', 'description']));

            DB::commit();
            return response()->json($product, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(ApiResponse::error(["data" => "Error updating product"]), 400);
        }
    }

    static function deleteById(Product $product)
    {
        if ($product->delete())
            return response()->json(ApiResponse::success("Deleted Successfully"), 200);
        return response()->json(ApiResponse::error(["data" => "Error deleting product"]), 400);
    }

    static function buyProduct(Request $request, Product $product)
    {


        try {
            ProductBill::createProductBill($product);

            return  response()->json(["message" => "Product Bill Created Successfully"], 200);
        } catch (\Exception $e) {
            return  response()->json(ApiResponse::error("Error Creating Product Bill"), 400);
        }
    }
}
