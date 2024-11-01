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
            // $table->foreignId('order_type_id')->references('id')->on('order_types')->onDelete('cascade');
            $table->bigInteger('order_type_id')->unsigned()->nullable();
            $table->foreign('order_type_id')->references('id')->on('order_types')->onDelete('cascade');
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
            $table->dropForeign(['order_type_id']);
            $table->dropColumn('order_type_id');
        });
    }
};
