<?php
namespace Jiny\Site\Chat;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

class JinySiteChatServiceProvider extends ServiceProvider
{
    private $package = "jiny-site-chat";
    public function boot()
    {
        // 모듈: 라우트 설정
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', $this->package);

        // 데이터베이스
        $this->loadMigrationsFrom(__DIR__.'/databases/migrations');

    }

    public function register()
    {
        /* 라이브와이어 컴포넌트 등록 */
        $this->app->afterResolving(BladeCompiler::class, function () {
            // 채팅방
            Livewire::component('site-chat-room',
                \Jiny\Site\Chat\Http\Livewire\SiteChatRoom::class);

            // 채팅 메시지
            Livewire::component('site-chat-message',
                \Jiny\Site\Chat\Http\Livewire\SiteChatMessage::class);

            // 채팅 유저
            Livewire::component('site-chat-user',
                \Jiny\Site\Chat\Http\Livewire\SiteChatUser::class);

        });
    }
}
