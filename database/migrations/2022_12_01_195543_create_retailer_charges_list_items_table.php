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
        Schema::create('retailer_charges_list_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('retailer_charges_list_id')->unsigned()->nullable();
            $table->foreign('retailer_charges_list_id')->references('id')->on('retailer_charges_lists')->onDelete('cascade');
            $table->float('max_volumetric_weight')->nullable();
            $table->float('min_volumetric_weight')->nullable();
            $table->decimal('price', $precision = 10, $scale = 2)->nullable();
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
        Schema::dropIfExists('retailer_charges_list_items');
    }
};
