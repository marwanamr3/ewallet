<?php

use Illuminate\Database\Seeder;

class VouchersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vouchers')->insert([

            'amount' => 10,
            'voucher_code' => 'DINT58SP%S91H',
            'valid' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('vouchers')->insert([

            'amount' => 50,
            'voucher_code' => 'JW49BG37gk594',
            'valid' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('vouchers')->insert([

            'amount' => 110,
            'voucher_code' => 'STHL06*M#N*41',
            'valid' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        DB::table('vouchers')->insert([

            'amount' => 200,
            'voucher_code' => 'AY35S*HB57BI1',
            'valid' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('vouchers')->insert([

            'amount' => 500,
            'voucher_code' => 'T8MH3P16X$R15',
            'valid' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
