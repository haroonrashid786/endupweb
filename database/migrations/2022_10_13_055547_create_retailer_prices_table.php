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
        Schema::create('retailer_prices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('retailer_id')->unsigned();
            $table->foreign('retailer_id')->references('id')->on('retailers')->onDelete('cascade');
            $table->decimal('extra_discount_percentage', $precision = 10, $scale = 2)->nullable();
            $table->decimal('extra_surcharge', $precision = 10, $scale = 2)->nullable();
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
        Schema::dropIfExists('retailer_prices');
    }
};
