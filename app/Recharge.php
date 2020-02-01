<?php

namespace App;

use App\Helpers\ApiResponse;
use App\Helpers\FailureCheck;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use mysql_xdevapi\Exception;
use Validator;
use Illuminate\Support\Facades\DB;
use Stripe;
use Session;
class Recharge extends Model implements FailureCheck
{
    public function Transaction()
   {
       return $this->morphOne('App\Transaction', 'transactionable');
   }

    public function rechargeable()
    {
        return $this->morphTo();
    }


    static function createRecharge(Request $request)
    {
        $failureCheck = Recharge::checkFailure([$request]);
        if (!$failureCheck[0])
            return  response()->json(ApiResponse::error($failureCheck[1]), 400);

        if($request['method']=='voucher')
        {
            $recharge = Voucher::checkVoucher($request->voucher_code);

//            dd($recharge);

            if(!$recharge[0])
                return response()->json(ApiResponse::error($recharge[1]), 400);

            try
            {
                DB::beginTransaction();
                $amount = $recharge[1]->amount;
                $user = auth()->user();

                $user->wallet += $amount;
                $user->total_income += $amount;
                $user->save();

                $recharge[1]->valid = 0;

                $rechargeable = new Recharge();
//                $rechargeable->method = $recharge[1]->method;

                $transaction = new Transaction();
                $transaction->customer_id = $user->id;
                $transaction->amount = $amount;
                $transaction->status = 'successful';

                $recharge[1]->save();
                $recharge[1]->recharge()->save($rechargeable);
                $rechargeable->transaction()->save($transaction);
                DB::commit();

                return response()->json("Recharge Successful", 200);
            }
            catch (Exception $e)
            {
                DB::rollback();
                return response()->json(ApiResponse::error($e), 400);
            }
        }
        else if($request['method'] == 'card'){


                Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                $stripeToken = \Stripe\Token::create(array(
                    "card" => array(
                        "number" => $request->input('card_number'),
                        "exp_month" => $request->input('exp_month'),
                        "exp_year" => $request->input('exp_year'),
                        "cvc" => $request->input('cvc'),
                    )));




                try
                {
                    DB::beginTransaction();
                    Stripe\Charge::create([
                        "amount" => $request->input('amount')*100,
                        "currency" => "usd",
                        "source" => $stripeToken,
                        "description" => "Test payment from itsolutionstuff.com.",

                    ]);

                    $amount = $request['amount'];
                    $user = auth()->user();

                    $user->wallet += $amount;

                    $user->total_income += $amount;

                    $user->save();

                    $rechargeable = new Recharge();
//                $rechargeable->method = $recharge[1]->method;

                    $transaction = new Transaction();
                    $transaction->customer_id = $user->id;

                    $transaction->amount = $amount;
                    $transaction->status = 'successful';

                    $recharge = new CreditCard(
                        ['company_name'=>'asdad','expiry_date'=>$request['exp_year']]
                    );
                    $recharge->save();
                    $recharge->recharge()->save($rechargeable);
                    $rechargeable->transaction()->save($transaction);
                    DB::commit();

                    return response()->json("Recharge Successful", 200);
                }
                catch (Exception $e)
                {
                    DB::rollback();
                    return response()->json(ApiResponse::error($e), 400);
                }

            }
            else {
                return response()->json(ApiResponse::error(["data" => "Payment failed"]), 400);
            }

        }




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
            'method' => 'required|in:voucher,card',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return  [false, $validator->errors()];
        }
        else
        {
            // validate method
            if($request['method'] == 'voucher')
            {
                $rules = [
                    'voucher_code' => 'required|size:13',
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails())
                    return  [false, $validator->errors()];
                else
                {
                    return [true];
                }


            }
            elseif(($request['method'] == 'card')){
                $validator = Validator::make($request->all(), [
                    "card_number" => "required",
                    "exp_month" => "required",
                    "exp_year" => "required",
                    "cvc" => "required",
                    "amount" => "required",
                ]);
                if ($validator->fails())
                    return  [false, $validator->errors()];
                else
                {
                    return [true];
                }

            }

        }


    }
}
