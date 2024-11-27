<?php
namespace Jiny\Site\Chat\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\WithFileUploads;

class SiteChatUser extends Component
{
    public $code;
    public $popupForm = false;
    public $popupWindowWidth = '2xl';
    public $forms = [];

    public $password;
    public $message;

    public $chat = [];


    public function mount()
    {
        $chat =DB::table('site_chat')->first();
        $this->chat = get_object_vars($chat); // 객체를 배열로 변환


    }

    public function render()
    {
        // 채팅방 참여자 확인
        $isParticipant = DB::table('site_chat_room')
            ->where('code', $this->code)
            ->where('email', Auth::user()->email)
            ->exists();

        if (!$isParticipant) {
            return view('jiny-site-chat::site.user.unauthorized');
        }

        $rows = DB::table('site_chat_room')->where('code', $this->code)->get();
        return view('jiny-site-chat::site.user.layout', [
            'rows' => $rows
        ]);
    }

    public function adduser()
    {
        $this->popupForm = true;
    }

    public function store()
    {
        if($this->forms['email']) {
            DB::table('site_chat_room')->insert([
                'code' => $this->code,
                'email' => $this->forms['email'],
                'lang' => $this->forms['lang']
            ]);

            // 채팅 수 증가
            DB::table('site_chat')
                ->where('code', $this->code)
                ->increment('user_cnt');
        }

        $this->forms = [];
        $this->popupForm = false;

        $this->dispatch('refresh-user');
    }

    public function edit($id)
    {
        $this->popupForm = true;
        $this->row = DB::table('site_chat_room')->where('id', $id)->first();
        $this->forms = get_object_vars($this->row);
    }

    public function update()
    {
        $id = $this->forms['id'];
        unset($this->forms['id']);

        DB::table('site_chat_room')
            ->where('id', $id)
            ->update($this->forms);

        $this->forms = [];
        $this->popupForm = false;

        $this->dispatch('refresh-user');
    }

    public function remove($id)
    {
        DB::table('site_chat_room')->where('id', $id)->delete();

        // 채팅 수 감소
        DB::table('site_chat')
            ->where('code', $this->code)
            ->decrement('user_cnt');

        $this->forms = [];
        $this->popupForm = false;
    }

    public function exit()
    {
        DB::table('site_chat_room')
            ->where('code', $this->code)
            ->where('email', Auth::user()->email)
            ->delete();

        // 채팅 수 감소
        DB::table('site_chat')
            ->where('code', $this->code)
            ->decrement('user_cnt');

        $this->dispatch('refresh-user');
    }

    public function checkPassword()
    {
        $row = DB::table('site_chat')->where('code', $this->code)->first();
        if($row->password && $this->password) {
            if($row->password == $this->password) {
                DB::table('site_chat_room')->insert([
                    'code' => $this->code,
                    'email' => Auth::user()->email,
                    'user_id' => Auth::user()->id
                ]);

                // 채팅 수 증가
                DB::table('site_chat')
                    ->where('code', $this->code)
                    ->increment('user_cnt');

                $this->password = null;
                $this->message = null;
            } else {
                $this->message = '비밀번호가 틀렸습니다.';
            }
        }
    }
}
