<?php

namespace App;

use App\Helpers\FailureCheck;
use App\Helpers\ApiResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
use mysql_xdevapi\Exception;
use Validator;
use Laravel\Scout\Searchable;

class Transaction extends Model
{
    public function transactionable()
    {
        return $this->morphTo();
    }

    use Searchable;

    protected $fillable = [
        'customer_id',
        'amount',
        'status',
        'transactionable_type',
        'transactionable_id',

    ];

    protected $casts = [
        'amount' => 'int'
    ];


    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public static function createTransfer($amount, User $user)
    {
        try {
            DB::beginTransaction();
            $transfer = Transfer::create(["receiver_id" => $user->id]);
            $transaction = new Transaction(["customer_id" => auth()->id(), "amount" => $amount, "status" => "successful"]);
            $transfer->transaction()->save($transaction);
            auth()->user()->wallet -= $amount;
            $authCustomer = Customer::find(auth()->id());
            $authCustomer->total_spendings += $amount;
            $authCustomer->save();
            $user->wallet += $amount;
            $user->total_income += $amount;
            auth()->user()->save();
            $user->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
        return true;
    }

    public static function createProductBill(Product $product, $reference)
    {
        try {
            DB::beginTransaction();
            $product_bill = new ProductBill();
            $product_bill->product_id = $product->id;

            $bill = new Bill();
            $bill->merchant_id = $product->merchant_id;
            $bill->bill_reference = $reference;

            $transaction = new Transaction();
            $transaction->customer_id = auth()->id();
            $transaction->amount = 0;
            $transaction->status = "awaiting confirmation";

            $product_bill->save();
            $product_bill->bill()->save($bill);
            $bill->transaction()->save($transaction);
            //            dd("nmarv");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return true;
    }

    public static function getAllCustomerTransactions(Request $request)
    {
        $allTransactions = Transaction::where('customer_id', '=', auth()->id());
        if ($request->input('offset') && $request->input('limit')) {
            $allTransactions = $allTransactions->offset($request->input('offset'))->limit($request->input('limit'));
        }
        $allTransactions = $allTransactions->get();
        foreach ($allTransactions as $transaction) {
            if ($transaction->transactionable_type === "App\\Bill") {
                $transaction->transactionable->billable;
            } elseif ($transaction->transactionable_type === "App\\Recharge") {
                $transaction->transactionable->rechargeable;
            } else {
                $transaction->transactionable;
            }
        }
        return $allTransactions;
    }

    public static function getAllMerchantTransactions(Request $request)
    {
        $allTransactions = Transaction::whereIn('transactionable_id', function ($query) {
            $query->select('id')
                ->from('bills')
                ->where('bills.merchant_id', auth()->id());
        })->get();

        foreach ($allTransactions as $transaction) {
            $transaction->transactionable->billable;
        }
        return $allTransactions;
    }
    public static function getAll(Request $request)
    {
        if (auth()->user()->type === "Customer") {
            $transactions = Transaction::getAllCustomerTransactions($request);
        } else {
            $transactions = Transaction::getAllMerchantTransactions($request);
        }

        // apply filter
        if ($request->filled('status')) {
            $transactions = $transactions->where('status', $request['status']);
        }
        if ($request->filled('type')) {
            $transactions = $transactions->where('transactionable_type', $request['type']);
        }
        if ($request->filled('date_from') and $request->filled('date_to')) {
            $transactions = $transactions->whereBetween('created_at', [ $request['date_from'] ,$request['date_to'] ]);
        }
        if ($request->filled('min') and $request->filled('max')) {
            $transactions = $transactions->whereBetween('amount', [ $request['min'] ,$request['max'] ]);
        }
        
        //apply search
        
        return response()->json($transactions, 200);
    }

    public static function getById(Transaction $transaction)
    {
        if ($transaction->transactionable_type === "App\\Bill") {
            $transaction->transactionable->billable;
        } elseif ($transaction->transactionable_type === "App\\Recharge") {
            $transaction->transactionable->rechargeable;
        } else {
            $transaction->transactionable;
        }
        return response()->json($transaction, 200);
    }
    public static function edit(Request $request, Transaction $transaction)
    {
        // determine user type to acquire the validation rules
        $userType = auth()->user()->type;
        $userId = auth()->id();
        // validate input
        if ($userType == 'Merchant') {
            // validate status
            $rules = [
                'status' => 'required|in:rejected,awaiting payment',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return  response()->json(ApiResponse::error($validator->errors()), 400);
            } else {
                // validate amount
                if ($request['status'] == 'awaiting payment') {
                    $rules = [
                        'amount' => 'required|numeric',
                    ];
                    $validator = Validator::make($request->all(), $rules);
                    if ($validator->fails()) {
                        return  response()->json(ApiResponse::error($validator->errors()), 400);
                    }
                    if ($request['amount'] <= 0) {
                        return response()->json(ApiResponse::error(["data" => "Amount has to be greater than 0"]), 400);
                    }
                } elseif ($request['status'] == 'rejected') {
                    $request['amount'] = 0;
                }
            }
        }
        // validate status
        elseif ($userType == 'Customer') {
            $rules = [
                'status' => 'required|in:successful,failed,awaiting confirmation',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return  response()->json(ApiResponse::error($validator->errors()), 400);
            }
        }

        // input data from request
        switch ($userType) {
            case 'Merchant':
                $input = $request->only(['amount', 'status']);
                break;
            case 'Customer':
                $input = $request->only(['status']);
                break;
        }

        // find the desired transaction and update

        try {
            $updatedTransaction = Transaction::findOrFail($transaction)->first();

            // if transaction is already closed, prevent update
            if ($userType == 'Customer' and $updatedTransaction->customer_id != $userId) {
                return response()->json(ApiResponse::error(["data" => "You are unauthorized to edit this transaction"]), 400);
            } elseif ($userType == 'Merchant' and $updatedTransaction->transactionable->merchant_id != $userId) {
                return response()->json(ApiResponse::error(["data" => "You are unauthorized to edit this transaction"]), 400);
            }
            if (in_array($updatedTransaction->status, ['rejected', 'failed', 'successful'], true)) {
                return response()->json(ApiResponse::error(["data" => "Transaction is closed, cannot modify"]), 400);
            } elseif ($userType == 'Customer' and $input['status'] == 'successful') {
                // - wallet customer + total spending customer, + wallet merchant + total income merchant
                if ($updatedTransaction->amount > auth()->user()->wallet) {
                    return  response()->json(ApiResponse::error(["data" => "Sorry, you don't have enough funds to complete this transaction"]), 400);
                }

                DB::beginTransaction();
                // debit customer's wallet
                auth()->user()->wallet -= $updatedTransaction->amount;
                auth()->user()->save();
                // add to customer's total spending
                $customer = Customer::find(auth()->id());
                $customer->total_spendings += $updatedTransaction->amount;
                $customer->save();
                // credit merchant's wallet and total_income
                $merchantId = $updatedTransaction->transactionable->merchant_id;
                $merchant = User::find($merchantId);
                $merchant->wallet += $updatedTransaction->amount;
                $merchant->total_income += $updatedTransaction->amount;
                $merchant->save();

                DB::commit();
            }

            $updatedTransaction->update($input);
            return response()->json($updatedTransaction, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(ApiResponse::error(["data" => "Error updating transaction"]), 400);
        }
    }
}
