<?php

use Illuminate\Database\Seeder;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $id = DB::table('users')->select('id')->where('users.username','=','Lucky')->get()->first()->id;

        DB::table('customers')->insert([
            'id' => $id,
            'first_name' => 'Lucky',
            'last_name' => 'John',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
