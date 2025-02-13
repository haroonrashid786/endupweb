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
            $table->string('number')->nullable();
            $table->tinyInteger('verified_by_collector')->default(0)->nullable();
            $table->tinyInteger('verified_by_rider')->default(0)->nullable();
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
            $table->dropColumn('number');
            $table->dropColumn('verified_by_collector');
            $table->dropColumn('verified_by_rider');
        });
    }
};
