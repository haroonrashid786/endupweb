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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('currency_id')->unsigned()->nullable();
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
            $table->string('city_from')->nullable();
            $table->string('city_to')->nullable();
            $table->string('country_from')->nullable();
            $table->string('country_to')->nullable();
            $table->string('postal_code_to')->nullable();
            $table->string('postal_code_from')->nullable();
            $table->float('volumetric_weight')->nullable();
            $table->float('length')->nullable();
            $table->float('height')->nullable();
            $table->float('width')->nullable();
            $table->float('quantity_box')->nullable();
            $table->decimal('price', $precision = 10, $scale = 2)->nullable();
            $table->bigInteger('shipping_terms_id')->unsigned()->nullable();
            $table->foreign('shipping_terms_id')->references('id')->on('shipping_terms')->onDelete('cascade');
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
        Schema::dropIfExists('prices');
    }
};
