<?php

namespace App;

use App\Helpers\FailureCheck;
use App\Helpers\ApiResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Validator;

class Transfer extends Model implements FailureCheck
{
    public function Transaction()
    {
        return $this->morphOne('App\Transaction', 'transactionable');
    }


    protected $fillable = [
        'id',
        'receiver_id',
        'created_at',
        'updated_at',

    ];


    /**
     * Validating and Checking Transfer
     *
     * @param  [ \Illuminate\Http\Request  $request ]
     * @return array [failed/success, errors/receiber ]
     */
    public static  function checkFailure($params)
    {
        $request = $params[0];

        //Validating Input
        $rules = [
            'receiver_username' => 'required',
            'amount' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return  [false, $validator->errors()];
        }
        //Checking if amount isn't negative
        if ( $request['amount'] <= 0)
            return  [false, "Amount has to be greater than 0"];
        //Checking if user has enough balance
        if ( $request['amount'] > auth()->user()->wallet) {
            return  [false, "Not enough balance"];
        }
        //Checking if receiver exists, user isnt receiver and receiver isn't a merchant
        $user = User::where('username', $request['receiver_username'])->first();
        if (!$user || $user->type == 'Merchant' || $user == auth()->user())
            return  [false, "Invalid User"];

        return [true, $user];
    }

    static function createTransfer(Request $request)
    {
        $failureCheck = Transfer::checkFailure([$request]);
        if (!$failureCheck[0])
            return  response()->json(ApiResponse::error($failureCheck[1]), 400);

        $user = $failureCheck[1];
        if (Transaction::createTransfer($request['amount'], $user)) {
            return  response()->json(["message" => "Success"], 200);
        } else {
            return  response()->json(ApiResponse::error("Error Occured"), 400);
        }
    }
}
