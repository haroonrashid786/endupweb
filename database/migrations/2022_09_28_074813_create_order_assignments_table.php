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
        Schema::create('order_assignments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('assigning_time')->nullable();
            $table->timestamp('store_pickup_scan_time')->nullable();
            $table->timestamp('depot_pickup_scan_time')->nullable();
            $table->string('delivery_type')->nullable();
            $table->string('sms_notify')->nullable();
            $table->string('email_notify')->nullable();
            $table->string('track_number')->nullable();
            $table->string('expected_time')->nullable();
            $table->text('rider_notes')->nullable();
            $table->string('code_delivery_zone')->nullable();
            $table->string('tracking_id')->nullable();
            $table->timestamp('delivery_at')->nullable();
            $table->text('notes')->nullable();
            $table->bigInteger('warehouse_id')->unsigned()->nullable();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
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
        Schema::dropIfExists('order_assignments');
    }
};
