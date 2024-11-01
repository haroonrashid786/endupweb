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
        Schema::table('order_assignments', function (Blueprint $table) {
            $table->dateTime('pickup_date_time')->nullable();
            $table->dateTime('dropoff_date_time')->nullable();
            $table->text('endup_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_assignments', function (Blueprint $table) {
            $table->dropColumn('pickup_date_time');
            $table->dropColumn('dropoff_date_time');
            $table->dropColumn('endup_notes');
        });
    }
};
