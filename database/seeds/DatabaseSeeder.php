<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(CustomersTableSeeder::class);
        $this->call(MerchantsTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(ServicesTableSeeder::class);
        $this->call(SubscriptionsTableSeeder::class);
        $this->call(ProductBillsTableSeeder::class);
        $this->call(ServiceBillsTableSeeder::class);
        $this->call(BillsTableSeeder::class);
        $this->call(TransactionsTableSeeder::class);
        $this->call(VouchersTableSeeder::class);
        

    }
}
