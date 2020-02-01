<?php

use Illuminate\Database\Seeder;

class ServiceBillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $service_id = DB::table('services')->select('id')->where('services.name','=','iCloud')->get()->first()->id;
        $customer_id = DB::table('users')->select('id')->where('users.username','=','Lucky')->get()->first()->id;

        DB::table('service_bills')->insert([
            'customer_id' => $customer_id,
            'service_id' => $service_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}
