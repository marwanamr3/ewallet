<?php

use Illuminate\Database\Seeder;

class SubscriptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customer_id = DB::table('users')->select('id')->where('users.username','=','Lucky')->get()->first()->id;
        $iCloud_service_id = DB::table('services')->select('id')->where('services.name','=','iCloud')->get()->first()->id;

        DB::table('subscriptions')->insert([
            'customer_id' => $customer_id,
            'service_id' => $iCloud_service_id,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
