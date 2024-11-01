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
        Schema::table('item_labels', function (Blueprint $table) {
            $table->tinyInteger('verified_by_return_collector')->nullable()->default(0);
            $table->tinyInteger('verified_by_return_collector_warehouse')->nullable()->default(0);
            $table->tinyInteger('verified_by_return_rider')->nullable()->default(0);
            $table->tinyInteger('verified_by_return_rider_warehouse')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_labels', function (Blueprint $table) {
            $table->dropColumn('verified_by_return_collector');
            $table->dropColumn('verified_by_return_collector_warehouse');
            $table->dropColumn('verified_by_return_rider');
            $table->dropColumn('verified_by_return_rider_warehouse');
        });
    }
};
