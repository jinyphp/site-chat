<?php
namespace Jiny\Site\Chat\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

use Jiny\WireTable\Http\Controllers\WireTablePopupForms;
class AdminChatRoom extends WireTablePopupForms
{
    public function __construct()
    {
        parent::__construct();
        $this->setVisit($this);

        ##
        $this->actions['table'] = "site_chat_room"; // 테이블 정보
        $this->actions['paging'] = 10; // 페이지 기본값

        $this->actions['view']['list'] = "jiny-site-chat::admin.chat_room.list";
        $this->actions['view']['form'] = "jiny-site-chat::admin.chat_room.form";

        $this->actions['title'] = "채팅 참여 인원";
        $this->actions['subtitle'] = "채팅방에 참여한 인원을 관리합니다.";

    }

    // ## 목록 dbFetch 전에 실행됩니다.
    // public function hookIndexing($wire)
    // {
    //     $code = $wire->request()['code'];
    //     $wire->actions['where']['code'] = $code;
    //     // 반환값이 있으면, 종료됩니다.
    // }

    public function index(Request $request)
    {
        $code = $request->code;

        $this->params['code'] = $code;
        //$this->actions['code'] = $code;

        // 검색조건 추가
        $this->actions['where']['code'] = $code;

        return parent::index($request);
    }



}
