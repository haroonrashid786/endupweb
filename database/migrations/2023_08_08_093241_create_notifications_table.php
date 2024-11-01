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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('end_user_id')->nullable();
            $table->foreign('end_user_id')->references('id')->on('end_users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('type')->nullable();
            $table->text('title')->nullable();
            $table->text('message')->nullable();
            $table->text('data')->nullable();
            $table->text('url')->nullable();
            $table->timestamp('read_at')->nullable();
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
        Schema::dropIfExists('notifications');
    }
};
