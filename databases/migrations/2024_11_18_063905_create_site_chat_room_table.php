<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 채팅방 팀원
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_chat_room', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // 채팅방 코드
            $table->string('code')->nullable();

            // 참가자
            $table->string('email')->nullable();
            $table->string('user_id')->nullable();

            $table->boolean('is_owner')->default(false); // 방장 여부


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_chat_room');
    }
};
