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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->tinyInteger('single_time')->default(0)->nullable();
            $table->string('value')->nullable();
            $table->tinyInteger('for_express')->default(0)->nullable();
            $table->tinyInteger('for_domestic')->default(0)->nullable();
            $table->tinyInteger('status')->default(1)->nullable();
            $table->date('date_start_expiry')->nullable();
            $table->date('date_end_expiry')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discounts');
    }
};
