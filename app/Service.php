<?php

namespace App;


use App\Helpers\ApiResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;


class Service extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    use searchable;
    
    protected $fillable = [
        'merchant_id', 'name', 'description', 'price', 'period', 'image'
    ];

    protected $casts = [
        'price' => 'float',
        'period' => 'int'
    ];

    public function merchant()
    {
        return $this->belongsTo('App\Merchant');
    }

    public function subscriptions()
    {
        return $this->hasMany('App\Subscription');
    }


    static function createService(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|Numeric',
            'period' => 'required|Numeric',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return  response()->json(ApiResponse::error($validator->errors()), 400);
        }
        if ($request['price'] <= 0)
            response()->json(ApiResponse::error(["data" => "Amount has to be greater than 0"]), 400);


        $request->merge(array("merchant_id" => auth()->id()));
        try {
            Service::create($request->all());
            return response()->json(ApiResponse::success("Created Successfully"), 201);
        } catch (\Exception $e) {
            return response()->json(ApiResponse::error(["data" => "Adding service failed"]), 400);
        }
    }

    static function showById(Service $service)
    {
        return response()->json($service, 200);
    }

    static function updateById(Request $request, Service $service)
    {
        $rules = [
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'price' => 'Numeric',
            'period' => 'sometimes|Numeric',
            'image' => 'sometimes|mimes:jpeg,bmp,png'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return  response()->json(ApiResponse::error($validator->errors()), 400);
        }
        if ($request['price'] <= 0)
            response()->json(ApiResponse::error(["data" => "Amount has to be greater than 0"]), 400);

        try {
            DB::beginTransaction();

            // update image - if given (Postman: uses form data not form-urlencoded)
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('images'); // Folder path is  e-wallet/storage/app/images
                $service->image =  explode('/', $path)[1];
            }

            $service->update($request->only(['name', 'description', 'price', 'period']));

            DB::commit();
            return response()->json($service, 200);
        } catch (\Exception $e) {
            return response()->json(ApiResponse::error(["data" => "Error updating service"]), 400);
        }
    }

    static function deleteById(Service $service)
    {
        try {
            $service->delete();
            return response()->json(ApiResponse::success("Deleted Successfully"), 200);
        } catch (\Exception $e) {
            return response()->json(ApiResponse::error(["data" => "Error deleting service"]), 400);
        }
    }

    static function subscribeUser(Service $service)
    {
        //Check if already subscribed to the service
        if (Subscription::where([['customer_id', '=', auth()->id()], ['service_id', '=', $service->id], ["status", "=", "active"]])->first())
            return response()->json(ApiResponse::error(["data" => "Already subscribed to this service"]), 400);
        //Create new subscription
        try {
            DB::beginTransaction();
            if (auth()->user()->wallet >= $service->price) {
                auth()->user()->wallet = auth()->user()->wallet - $service->price;
                $customer = Customer::find(auth()->id());
                $customer->total_spendings+=$service->price;
                Subscription::create(["customer_id" => auth()->id(), "service_id" => $service->id, "status" => "active"]);
                auth()->user()->save();
                $customer->save();
                DB::commit();
                return response()->json(ApiResponse::success("Subscribed Successfully"), 200);
            }
            else {
                return response()->json(ApiResponse::error(["data" => "Insufficient Funds"]), 400);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(ApiResponse::error(["data" => "Error subscribing to service"]), 400);
        }
    }
}
