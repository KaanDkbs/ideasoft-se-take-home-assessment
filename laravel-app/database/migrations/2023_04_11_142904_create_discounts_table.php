<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->enum('condition_type', ['total_order_amount', 'category_id']); // yeni indirim kurallarına göre burası set edilmesi gerekir
            $table->integer('category_id')->nullable();
            $table->decimal('condition_value', 10,2)->default(0.00);
            $table->enum('discount_type', ['percentage', 'fixed_amount', 'product_amount', 'cheapest_product_percentage']); // yeni indirim kurallarına göre burası set edilmesi gerekir
            $table->decimal('discount_value', 10, 2)->default(0.00);
            $table->integer('free_product_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discounts');
    }
}
