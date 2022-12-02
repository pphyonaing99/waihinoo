<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('supplier_id');
            $table->string('serial_number');
            $table->string('model_number');
            $table->string('color');
            $table->string('size');
            $table->integer('instock_qty');
            $table->integer('purchase_price');
            $table->string('purchase_currency');
            $table->integer('sales_price');
            $table->string('sales_currency');
            $table->integer('exchange_rate');
            $table->integer('discount_flag');
            $table->integer('discount_percent')->nullable();
            $table->integer('foc_item_flag');
            $table->integer('custom_discount_flag');
            $table->unsignedBigInteger('custom_discount_id')->nullable();
            $table->string('specification_description');
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
        Schema::dropIfExists('accessories');
    }
}
