<?php

use Illuminate\Database\Seeder;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customer_id = DB::table('users')->select('id')->where('users.username','=','Lucky')->get()->first()->id;
        
        // Product bill transaction
        $product_id = DB::table('products')->select('id')->where('products.name','=','iPhone 7 Plus')->get()->first()->id;
        $product_bills_id = DB::table('product_bills')->select('id')->where('product_bills.product_id','=',$product_id)->get()->first()->id;
        $bills_id =  DB::table('bills')->select('id')->where('bills.billable_id','=',$product_bills_id)->where('bills.billable_type','=','App\ProductBill')->get()->first()->id;

        DB::table('transactions')->insert([
            'customer_id' => $customer_id,
            'status' => 'awaiting confirmation',
            'transactionable_type' => 'App\Bill',
            'transactionable_id' => $bills_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Service bill transaction
        $service_id = DB::table('services')->select('id')->where('services.name','=','iCloud')->get()->first()->id;
        $service_bill_id = DB::table('service_bills')->select('id')->where('service_bills.service_id','=',$service_id)->get()->first()->id;
        $bills_id =  DB::table('bills')->select('id')->where('bills.billable_id','=',$service_bill_id)->where('bills.billable_type','=','App\ServiceBill')->get()->first()->id;
        $service_price = DB::table('services')->select('price')->where('services.name','=','iCloud')->get()->first()->price;
        DB::table('transactions')->insert([
            'customer_id' => $customer_id,
            'amount' => $service_price,
            'transactionable_type' => 'App\Bill',
            'transactionable_id' => $bills_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
