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
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::table('orders', function (Blueprint $table) {
            $table->bigInteger('retailer_id')->unsigned()->nullable()->change();
        });
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Schema::table('orders', function (Blueprint $table) {
                $table->bigInteger('retailer_id')->unsigned()->nullable(false)->change();
            });
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
        });
    }
};
