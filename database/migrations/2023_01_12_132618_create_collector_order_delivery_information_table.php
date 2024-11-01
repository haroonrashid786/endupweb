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
        Schema::create('collector_order_delivery_information', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('order_id')->unsigned();
            // $table->integer('order_id')->unsigned()->index();
            $table->string('order_qr_code')->nullable();
            // $table->string('signature')->nullable();
            $table->string('pacakge_image')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collector_order_delivery_information');
    }
};
