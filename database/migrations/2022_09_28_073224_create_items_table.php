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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->string('sku')->nullable();
            $table->string('name')->nullable();
            $table->string('barcode')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', $precision = 10, $scale = 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->float('weight')->nullable();
            $table->float('length')->nullable();
            $table->float('dimension')->nullable();
            $table->float('height')->nullable();
            $table->float('volumetric_weight')->nullable();
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
        Schema::dropIfExists('items');
    }
};
