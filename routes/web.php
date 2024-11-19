<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::middleware(['web','auth'])
->name('chat.')
->prefix('/home/chat')->group(function () {
    Route::get('/',[\Jiny\Site\Chat\Http\Controllers\SiteChat::class,
        "index"]);

    Route::get('/message/{code}',[\Jiny\Site\Chat\Http\Controllers\SiteChatMessage::class,
        "index"]);

    Route::get('/invite/{code}',[\Jiny\Site\Chat\Http\Controllers\SiteChatInvite::class,
        "index"]);

    // Route::get('/partner/type',[\Jiny\Site\Http\Controllers\Admin\AdminPartnerType::class,
    //     "index"]);
    // Route::get('/partner/invoice',[\Jiny\Site\Http\Controllers\Admin\AdminPartnerInvoice::class,
    //     "index"]);
});


// 채팅 업로드 파일 출력
// 자신의 방에서 업로드한 이미지만 접근 가능합니다.
Route::middleware(['web'])
->name('chat.image.')
->prefix('/home/chat')->group(function () {
    Route::get('/message/{code}/{file}', [
        \Jiny\Site\Chat\Http\Controllers\AssetsController::class,
        'index']);
});
