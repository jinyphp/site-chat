<?php
namespace Jiny\Site\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/**
 * 사용자 로그인
 * 채팅 서비스 목록
 */
use Jiny\Site\Http\Controllers\SiteController;
class SiteChat extends SiteController
{
    public function __construct()
    {
        parent::__construct();
        $this->setVisit($this);

        // 채팅 서비스 레이아웃
        $this->actions['view']['layout']
            = inSlotView("home.chat",
                "jiny-site-chat::site.chat.layout");

    }

}