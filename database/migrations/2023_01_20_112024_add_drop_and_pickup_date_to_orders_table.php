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
            $table->date("pickupdate")->nullable();
            $table->time("pickuptime")->nullable();
            $table->date("deliverydate")->nullable();
            $table->time("deliverytime")->nullable();
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
            $table->dropColumn("pickupdate");
            $table->dropColumn("pickuptime");
            $table->dropColumn("deliverydate");
            $table->dropColumn("deliverytime");
        });
    }
};
