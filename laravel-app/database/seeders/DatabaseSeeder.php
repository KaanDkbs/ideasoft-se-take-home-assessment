<?php

namespace Database\Seeders;

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
        $this->call([
            UsersTableSeeder::class,
            ProductsSeeder::class,
            CustomersSeeder::class,
            DiscountsSeeder::class,
        ]);
    }
}


//1. Toplam tutar'a indirim oranı
//2. id'ye göre kaç tane satın aldy
