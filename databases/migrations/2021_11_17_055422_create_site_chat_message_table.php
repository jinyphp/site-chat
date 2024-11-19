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
        Schema::create('site_chat_message', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('partner_id')->nullable();
            $table->string('partner_name')->nullable();

            $table->text('message')->nullable(); // 메시지 내용
            $table->string('image')->nullable(); // 이미지 경로
            $table->string('sender_id')->nullable(); // 발신자 ID
            $table->string('receiver_id')->nullable(); // 수신자 ID
            $table->enum('direction', ['send', 'receive'])->default('send'); // 메시지 방향 (발신/수신)
            $table->timestamp('read_at')->nullable(); // 읽은 시간

            $table->string('user_id')->nullable();
            $table->string('user_name')->nullable();

            // 파트너 관리자
            $table->string('manager')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_chat_message');

    }
};
