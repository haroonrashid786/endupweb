<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_working_days', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('rider_id')->unsigned();
            $table->foreign('rider_id')->references('id')->on('riders')->onDelete('cascade');
            $table->bigInteger('working_day_id')->unsigned();
            $table->foreign('working_day_id')->references('id')->on('working_days')->onDelete('cascade');

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
        Schema::dropIfExists('rider_working_days');
    }
};
