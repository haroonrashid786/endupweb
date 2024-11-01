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
        Schema::create('end_users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname')->nullable();
            $table->string("lastname")->nullable();
            $table->string("email")->unique()->nullable();
            $table->string('number')->unique()->nullable();
            $table->string('password');
            $table->string("location_str")->nullable();
            $table->string("location_cod")->nullable();
            $table->string("code")->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('end_users');
    }
};
