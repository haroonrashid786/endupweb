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
        Schema::create('retailer_charges', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('retailer_charges_list_id')->unsigned()->nullable();
            $table->foreign('retailer_charges_list_id')->references('id')->on('retailer_charges_lists')->onDelete('cascade');
            $table->bigInteger('retailer_id')->unsigned()->nullable();
            $table->foreign('retailer_id')->references('id')->on('retailers')->onDelete('cascade');
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
        Schema::dropIfExists('retailer_charges');
    }
};
