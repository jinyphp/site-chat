<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * 채팅 메인 페이지
 */
Route::middleware(['web','auth'])
->name('chat.')
->prefix('/home/chat')->group(function () {
    Route::get('/',[\Jiny\Site\Chat\Http\Controllers\SiteChat::class,
        "index"]);

    Route::get('/message/{code}',[\Jiny\Site\Chat\Http\Controllers\SiteChatMessage::class,
        "index"]);

    Route::get('/invite/{code}',[\Jiny\Site\Chat\Http\Controllers\SiteChatInvite::class,
        "index"]);
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



if(function_exists('admin_prefix')) {
    $prefix = admin_prefix();

    Route::middleware(['web','auth', 'admin'])
    ->name('admin.chat')
    ->prefix($prefix.'/chat')->group(function () {

        Route::get('/',[\Jiny\Site\Chat\Http\Controllers\Admin\AdminChat::class,
            "index"]);

        Route::get('/lang',[\Jiny\Site\Chat\Http\Controllers\Admin\AdminChatLang::class,
            "index"]);

        Route::get('/room/{code}',[\Jiny\Site\Chat\Http\Controllers\Admin\AdminChatRoom::class,
            "index"]);

    });
}
