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
        Schema::create('shipping_terms', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('is_free')->default(0)->nullable();
            $table->tinyInteger('is_express')->default(0)->nullable();
            $table->tinyInteger('is_domestic')->default(0)->nullable();
            $table->tinyInteger('is_international')->default(0)->nullable();
            $table->bigInteger('discount_id')->unsigned()->nullable();
            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade');
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
        Schema::dropIfExists('shipping_terms');
    }
};
