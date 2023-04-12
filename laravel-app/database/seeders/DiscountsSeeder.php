<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Discounts;

class DiscountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Senaryo 1: Toplam sipariş tutarı 1000 TL ve üzerinde olan müşteriye %10 indirim uygulanır.
        $discountRule = new Discounts;
        $discountRule->name = '10_PERCENT_OVER_1000';
        $discountRule->description = 'Toplam sipariş tutarı 1000 TL ve üzerinde olan müşteriye %10 indirim uygulanır.';
        $discountRule->condition_type = 'total_order_amount';
        $discountRule->condition_value = 1000;
        $discountRule->discount_type = 'percentage';
        $discountRule->discount_value = 10;
        $discountRule->save();

        // Senaryo 2: 2 ID'li kategoriye ait bir üründen 6 adet satın alındığında, bir tanesi ücretsiz olarak verilir.
        $discountRule = new Discounts;
        $discountRule->name = 'BUY_5_GET_1';
        $discountRule->description = '2 ID\'li kategoriye ait bir üründen 6 adet satın alındığında, bir tanesi ücretsiz olarak verilir.';
        $discountRule->condition_type = 'category_id';
        $discountRule->category_id = 2;
        $discountRule->condition_value = 6;
        $discountRule->discount_type = 'product_amount';
        $discountRule->free_product_count = 1;
        $discountRule->save();

        // Senaryo 3: 1 ID'li kategoriden iki veya daha fazla ürün satın alındığında, en ucuz ürüne %20 indirim yapılır.
        $discountRule = new Discounts;
        $discountRule->name = 'BUY_2_CHEAPEST_20_PERCENT';
        $discountRule->description = '1 ID\'li kategoriden iki veya daha fazla ürün satın alındığında, en ucuz ürüne %20 indirim yapılır.';
        $discountRule->condition_type = 'category_id';
        $discountRule->category_id = 1;
        $discountRule->condition_value = 2.00;
        $discountRule->discount_type = 'cheapest_product_percentage';
        $discountRule->discount_value = 20;
        $discountRule->save();
    }
}
