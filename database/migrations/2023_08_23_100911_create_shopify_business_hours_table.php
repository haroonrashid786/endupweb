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
        Schema::create('shopify_business_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shopify_package_id')->nullable();
            $table->foreign('shopify_package_id')->references('id')->on('shopify_packages')->onDelete('cascade')->onUpdate('cascade');
            $table->string('day')->nullable();
            $table->time('open_time')->nullable();
            $table->time('break_time_start')->nullable();
            $table->time('break_time_end')->nullable();
            $table->time('close_time')->nullable();
            $table->string('time_zone')->nullable();
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
        Schema::dropIfExists('shopify_business_hours');
    }
};
