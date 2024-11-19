<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 채팅방 목록
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
        Schema::create('site_chat', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // 채팅방 코드
            $table->string('code')->nullable();
            // 채팅방 비밀번호
            $table->string('password')->nullable();


            $table->string('image')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            // 배경색
            $table->string('bg_color')->default('bg-gray-50');
            // 보낸 메시지 색
            $table->string('send_color')->default('bg-yellow-200');
            // 받은 메시지 색
            $table->string('receive_color')->default('bg-blue-200');

            // 초대 코드
            $table->string('invite')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_chat');
    }
};
