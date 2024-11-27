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
            // 채팅언어
            $table->string('lang')->default('ko');

            // 참가자 정보
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->string('user_id')->nullable();

            $table->boolean('is_owner')->default(false); // 방장 여부

            $table->string('llm')->nullable();
            $table->string('position')->nullable();
            $table->text('description')->nullable();


            // 메시지 확인
            $table->timestamp('last_checked_at')->nullable(); // 마지막 확인 일자
            $table->timestamp('last_message_at')->nullable(); // 마지막 메시지 일자
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
