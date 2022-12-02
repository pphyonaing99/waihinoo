<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('supplier_id');
            $table->integer('product_flag');
            $table->unsignedInteger('product_id')->nullable()->comment('flag 0 for phone,1 for accessory');
            $table->integer('purchase_quantity');
            $table->unsignedBigInteger('purchase_by');
            $table->date('purchase_date')->nullable();
            $table->string('purchase_type')->nullable();
            $table->double('timetick');
            $table->text('description');
            $table->integer('exchange_rate')->default(0);
            $table->integer('amount')->default(0);
            $table->integer('total_amount')->default(0);
            $table->string('currency_type')->nullable();
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
        Schema::dropIfExists('purchases');
    }
}
