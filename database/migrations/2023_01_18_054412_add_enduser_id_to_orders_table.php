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
            // $table->foreignId('enduser_id')->references('id')->on('end_users')->onDelete('cascade');

            $table->bigInteger('enduser_id')->unsigned()->nullable();
            $table->foreign('enduser_id')->references('id')->on('end_users')->onDelete('cascade');
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
            $table->dropForeign(['enduser_id']);
            $table->dropColumn('enduser_id');
        });
    }
};
