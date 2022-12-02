<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangeProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_products', function (Blueprint $table) {
            $table->id();
            $table->integer('product_flag')->comment('1 for product, 2 for accessory');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->string('imei_number')->nullable();
            $table->unsignedBigInteger('accessory_id')->nullable();
            $table->string('accessory_name')->nullable();
            $table->integer('qty')->nullable();
            $table->string('comment')->nullable();
            $table->date('exchange_date');
            $table->unsignedBigInteger('supplier_id');
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
        Schema::dropIfExists('exchange_products');
    }
}
