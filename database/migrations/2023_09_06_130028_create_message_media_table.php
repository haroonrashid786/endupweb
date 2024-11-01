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
        Schema::create('message_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('messages_id');
            $table->string('path');
            $table->string('type')->nullable();
            $table->timestamps();
            $table->foreign('messages_id')
                ->references('id')
                ->on('messages')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_media');
    }
};
