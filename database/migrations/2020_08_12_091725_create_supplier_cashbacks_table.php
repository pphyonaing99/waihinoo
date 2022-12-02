<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierCashbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_cashbacks', function (Blueprint $table) {
            $table->id();
            $table->integer('voucher_number');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->integer('item_flag')->comment('0 = product , 1 = accessory')->default(0);
            $table->bigInteger('cashback');
            $table->date('date');
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
        Schema::dropIfExists('supplier_cashbacks');
    }
}
