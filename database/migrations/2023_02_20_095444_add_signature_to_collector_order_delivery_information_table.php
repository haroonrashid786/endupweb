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
        Schema::table('collector_order_delivery_information', function (Blueprint $table) {
            $table->string('signature')->nullable();
            $table->string('return_pacakge_image')->nullable();
            $table->string('return_signature')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('collector_order_delivery_information', function (Blueprint $table) {
            $table->dropColumn('signature');
            $table->dropColumn('return_pacakge_image');
            $table->dropColumn('return_signature');
        });
    }
};
