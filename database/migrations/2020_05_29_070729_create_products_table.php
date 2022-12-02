<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('supplier_id');
            $table->string('imei_number');
            $table->string('model_number');
            $table->string('color');
            $table->string('size');
            $table->integer('instock_qty');
            $table->integer('reorder_qty');
            $table->integer('purchase_price');
            $table->string('purchase_currency');
            $table->integer('sales_price');
            $table->string('sales_currency');
            $table->integer('discount_flag');
            $table->string('photo');
            $table->integer('gift_flag');
            $table->string('gift_item_id')->nullable();
            $table->integer('custom_discount_flag');
            $table->unsignedBigInteger('custom_discount_id')->nullable();
            $table->string('specification_description');
            $table->integer('discount_percent')->nullable();
            $table->integer('series_flag')->default(0);
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
        Schema::dropIfExists('products');
    }
}
