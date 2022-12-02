<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('phone');
            $table->integer('advance_balance')->default(0);
            $table->integer('credit_balance')->default(0);
            $table->text('frequent_item')->nullable();
            $table->string('email')->nullable();
            $table->integer('created_by');
            $table->integer('credit_flag')->default(0);
            $table->integer('credit_limit')->default(0);
            $table->integer('allow_credit_limit')->default(0);
            $table->integer('allow_credit_period')->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
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
        Schema::dropIfExists('customers');
    }
}
