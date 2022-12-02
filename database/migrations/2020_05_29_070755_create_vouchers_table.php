<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->integer('voucher_number');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('sold_by');
            $table->longText('item_list');
            $table->longText('accessory_list');
            $table->integer('total_amount');
            $table->integer('tax');
            $table->integer('total_discount');
            $table->integer('voucher_grand_total');
            $table->integer('total_quantity');
            $table->string('payment_type');
            $table->date('date');
            $table->integer('print_flag')->default(0);
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
        Schema::dropIfExists('vouchers');
    }
}
