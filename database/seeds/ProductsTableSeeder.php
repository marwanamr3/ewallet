<?php

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $merchant_id = DB::table('users')->select('id')->where('users.username','=','Steve')->get()->first()->id;

        DB::table('products')->insert([
            'merchant_id' => $merchant_id,
            'name' => 'iPhone 7 Plus',
            'description' => 'Amazing smartphone that is totally different and advanced than previous models.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('products')->insert([
            'merchant_id' => $merchant_id,
            'name' => 'iPhone X',
            'description' => 'Amazing smartphone that is totally different and advanced than previous models.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('products')->insert([
            'merchant_id' => $merchant_id,
            'name' => 'iPhone XS MAX Plus',
            'description' => 'Amazing smartphone that is totally different and advanced than previous models.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
