<?php

use Illuminate\Database\Seeder;

class BillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $merchant_id = DB::table('merchants')->select('id')->where('merchants.name','=','Apple')->get()->first()->id;
        $product_id = DB::table('products')->select('id')->where('products.name','=','iPhone 7 Plus')->get()->first()->id;
        $service_id = DB::table('services')->select('id')->where('services.name','=','iCloud')->get()->first()->id;
        
        // Product bill
        $billable_id = DB::table('product_bills')->select('id')->where('product_bills.product_id','=',$product_id)->get()->first()->id;
        
        DB::table('bills')->insert([
            'merchant_id' => $merchant_id,
            'bill_reference' =>'ABC123',
            'billable_type' => 'App\ProductBill',
            'billable_id' => $billable_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Service bill
        $billable_id = DB::table('service_bills')->select('id')->where('service_bills.service_id','=',$service_id)->get()->first()->id;
        
        DB::table('bills')->insert([
            'merchant_id' => $merchant_id,
            'bill_reference' =>'DEF456',
            'billable_type' => 'App\ServiceBill',
            'billable_id' => $billable_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
