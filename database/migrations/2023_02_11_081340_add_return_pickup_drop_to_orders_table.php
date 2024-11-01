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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('return_dropoff_coordinates')->nullable();
            $table->string('return_pickup_coordinates')->nullable();
            $table->string('return_distance')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('return_dropoff_coordinates');
            $table->dropColumn('return_pickup_coordinates');
            $table->dropColumn('return_distance');
        });
    }
};
