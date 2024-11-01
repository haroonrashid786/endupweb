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
        Schema::table('retailer_charges_list_items', function (Blueprint $table) {
            $table->unsignedBigInteger('shopify_package_id')->nullable();
            $table->foreign('shopify_package_id')->references('id')->on('shopify_packages')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retailer_charges_list_items', function (Blueprint $table) {
            $table->dropForeign(['shopify_package_id']);
            $table->dropColumn('shopify_package_id');
        });
    }
};
