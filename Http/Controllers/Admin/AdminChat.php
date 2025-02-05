<?php
namespace Jiny\Site\Chat\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;


use Jiny\Admin\Http\Controllers\AdminController;
class AdminChat extends AdminController
{
    public function __construct()
    {
        // 라이센스 키 설정
        $this->licenseKey = "chat";

        parent::__construct();
        $this->setVisit($this);

        ##
        $this->actions['table']['name'] = "site_chat"; // 테이블 정보
        $this->actions['paging'] = 10; // 페이지 기본값

        $this->actions['view']['list'] = "jiny-site-chat::admin.chat.list";
        $this->actions['view']['form'] = "jiny-site-chat::admin.chat.form";

        $this->actions['title'] = "채팅목록";
        $this->actions['subtitle'] = "개설된 채팅방을 관리합니다.";
    }

}
