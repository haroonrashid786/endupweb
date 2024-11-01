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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('retailer_id')->unsigned();
            $table->foreign('retailer_id')->references('id')->on('retailers')->onDelete('cascade');
            $table->string('order_number')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('shipping_notes')->nullable();
            $table->string('enduser_name')->nullable();
            $table->string('enduser_email')->nullable();
            $table->string('enduser_mobile')->nullable();
            $table->string('enduser_address')->nullable();
            $table->string('enduser_ordernotes')->nullable();
            $table->string('time_placed')->nullable();
            $table->bigInteger('warehouse_id')->unsigned()->nullable();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            
            $table->softDeletes();
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
        Schema::dropIfExists('orders');
    }
};
