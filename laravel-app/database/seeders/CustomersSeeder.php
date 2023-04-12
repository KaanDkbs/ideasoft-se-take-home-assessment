<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Customers;

class CustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = json_decode(File::get("database/data/customers.json"));
        foreach ($products as $product) {
            Customers::create([
                'name' => $product->name,
                'since' => $product->since,
                'revenue' => $product->revenue,
            ]);
        }
    }
}
