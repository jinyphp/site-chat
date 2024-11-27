<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 채팅방 언어
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
        Schema::create('site_chat_lang', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('lang')->nullable();
            $table->string('name')->nullable();

            $table->integer('cnt')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_chat_lang');
    }
};
