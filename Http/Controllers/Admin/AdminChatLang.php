<?php
namespace Jiny\Site\Chat\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

use Jiny\Admin\Http\Controllers\AdminController;
class AdminChatLang extends AdminController
{
    public function __construct()
    {
        // 라이센스 키 설정
        $this->licenseKey = "chat";

        parent::__construct();
        $this->setVisit($this);

        ##
        $this->actions['table']['name'] = "site_chat_lang"; // 테이블 정보
        $this->actions['paging'] = 10; // 페이지 기본값

        $this->actions['view']['list'] = "jiny-site-chat::admin.chat_lang.list";
        $this->actions['view']['form'] = "jiny-site-chat::admin.chat_lang.form";

        $this->actions['title'] = "채팅언어";
        $this->actions['subtitle'] = "채팅언어를 관리합니다.";

    }

}
