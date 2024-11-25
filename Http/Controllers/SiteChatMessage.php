<?php
namespace Jiny\Site\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

use Jiny\Site\Http\Controllers\SiteController;
class SiteChatMessage extends SiteController
{
    public function __construct()
    {
        parent::__construct();
        $this->setVisit($this);

        $this->actions['view']['layout']
            = inSlotView("home.chat_message",
                "jiny-site-chat::site.chat_message.layout");
    }

    public function index(Request $request)
    {
        $code = $request->code;

        // 채팅방 존재 여부 확인
        $chat = DB::table('site_chat')
        ->where('code', $code)
        ->first();
        if(!$chat) {
            return view('jiny-site-chat::site.chat_message.error',[
                'message' => '존재하지 않는 채팅방 입니다.',
            ]);
        }

        // 채팅방 데이터베이스 파일 경로 설정
        $path = database_path('chat');
        $path .= DIRECTORY_SEPARATOR.substr($code,0,2);
        $path .= DIRECTORY_SEPARATOR.substr($code,2,2);
        $path .= DIRECTORY_SEPARATOR.substr($code,4,2);

        // 채팅방 데이터베이스 파일 존재 여부 확인
        $dbfile = $path.DIRECTORY_SEPARATOR.$code.'.sqlite';
        if(!file_exists($dbfile)) {
            return view('jiny-site-chat::site.chat_message.error', [
                'message' => 'chat db not found'
            ]);
        }

        $this->params['code'] = $code;
        $this->params['dbfile'] = $dbfile;
        $this->params['chat'] = $chat;
        return parent::index($request);
    }

}
