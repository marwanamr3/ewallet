<?php

use Illuminate\Database\Seeder;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $merchant_id = DB::table('users')->select('id')->where('users.username','=','Steve')->get()->first()->id;

        DB::table('services')->insert([
            'merchant_id' => $merchant_id,
            'name' => 'iCloud',
            'description' => 'Best deal, lower value.',
            'price' => 19.99,
            'period' => 30,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('services')->insert([
            'merchant_id' => $merchant_id,
            'name' => 'iCloud Ultimate',
            'description' => 'Best deal, lowest value.',
            'price' => 120,
            'period' => 365,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
