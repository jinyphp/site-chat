<?php
namespace Jiny\Site\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

use Jiny\Site\Http\Controllers\SiteController;
use Jiny\License\Http\Controllers\LicenseController;
class SiteChatInvite extends LicenseController
{
    public function __construct()
    {
        // 라이센스 키 설정
        $this->licenseKey = "chat";

        parent::__construct();
        $this->setVisit($this);

        $this->actions['view']['layout'] = "jiny-site-chat::site.chat.layout";
    }

    public function index(Request $request)
    {
        $hash = $request->code;

        // 초대 코드 체크
        $row = DB::table('site_chat')->where('invite',$hash)->first();
        if($row) {

            $code = substr($hash, 0, 8);

            // 중복 등록 체크
            $exists = DB::table('site_chat_room')
                ->where('code', $code)
                ->where('email', Auth::user()->email)
                ->exists();

            if (!$exists) {
                // 채팅방 추가
                DB::table('site_chat_room')->insert([
                    'code' => $code,
                    'email' => Auth::user()->email
                ]);
            }

            // 채팅방 이동
            return redirect('/home/chat/message/'.$code);
        }


        return parent::index($request);
    }

}
