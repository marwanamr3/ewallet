<?php

use Illuminate\Database\Seeder;

class ProductBillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product_id = DB::table('products')->select('id')->where('products.name','=','iPhone 7 Plus')->get()->first()->id;

        DB::table('product_bills')->insert([
            'product_id' => $product_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
