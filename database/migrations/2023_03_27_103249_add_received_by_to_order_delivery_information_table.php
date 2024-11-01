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
        Schema::table('order_delivery_information', function (Blueprint $table) {
            //
            $table->string('received_by')->nullable();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_delivery_information', function (Blueprint $table) {
            $table->dropColumn('received_by');
            $table->dropColumn('name');
            $table->dropColumn('address');
        });
    }
};
