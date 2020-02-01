<?php

use Illuminate\Database\Seeder;

class MerchantsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $id = DB::table('users')->select('id')->where('users.username','=','Steve')->get()->first()->id;

        DB::table('merchants')->insert([
            'id' => $id,
            'name' => 'Apple',
            'address' => '1 Apple Park Way Cupertino, California, USA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
