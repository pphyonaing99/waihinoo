<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAeonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aeons', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('nrc');
            $table->string('details_document')->nullable();
            $table->string('job_position');
            $table->integer('salary');
            $table->string('job_reference_letter')->nullable();
            $table->string('reporter_reference_letter')->nullable();
            $table->integer('installment_plan');
            $table->unsignedBigInteger('product_id');
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
        Schema::dropIfExists('aeons');
    }
}
