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
use Livewire\Attributes\On;


/**
 * 채팅방 목록을 출력합니다.
 * 자신이 속해 있는 채팅방만 출력합니다.
 */
class SiteChatRoom extends Component
{
    public $code;

    public $user_id;
    public $user_name;
    public $user_email;

    use WithFileUploads;

    public $popupForm = false;
    public $popupEdit = false;
    public $popupDelete = false;
    public $delete_password;
    public $popupWindowWidth = '2xl';
    public $message;
    public $forms = [];

    public $viewFile;

    public function mount()
    {
        $user = Auth::user();
        $this->user_name = $user->name;
        $this->user_email = $user->email;
        $this->user_id = $user->id;

        if(!$this->viewFile) {
            $this->viewFile = 'jiny-site-chat::site.chat.room';
        }
    }

    public function render()
    {
        // 나의 채팅방
        $rows = $this->myChat();
        $rows = $this->myChatRoom($rows); // 권환체크


        // 마지막 확인 시간 추가
        foreach($rows as &$row) {
            //dump($row);
            $room = DB::table('site_chat_room')
                ->where('code', $row->code)
                ->where('email', $this->user_email)
                ->first();
            //dump($this->user_id);
            //dump($room);
            if($room) {
                $row->last_checked_at = $room->last_checked_at;
            } else {
                $row->last_checked_at = '';
            }
        }

        //dd($rows);


        return view($this->viewFile,[
            'rooms' => $rows
        ]);
    }

    /**
     * 나의 채팅방
     */
    private function myChat()
    {
        $rows = DB::table('site_chat')
            ->join('site_chat_room', 'site_chat.code', '=', 'site_chat_room.code')
            ->where('site_chat_room.email', Auth::user()->email)
            ->select('site_chat.*')
            ->orderBy('last_message_at', 'desc') // 마지막 메시지 순서
            ->paginate(8);

        return $rows;
    }

    /**
     * owner
     * 나의 채팅방 권환체크
     */
    private function myChatRoom($rows)
    {
        // 채팅방 정보, 권환체크
        $rooms = DB::table('site_chat_room')
            ->where('email', Auth::user()->email)
            ->get();

        $rooms = $rooms->keyBy('code');

        foreach($rows as &$row) {
            $code = $row->code;
            $row->is_owner = $rooms[$code]->is_owner;
        }

        return $rows;
    }

    /**
     * 새로운 채팅방 개설
     */
    #[On('room-created')]
    public function create()
    {
        $this->popupForm = true;
        $this->forms = [];
    }

    /**
     * 새로운 채팅방 저장
     */
    public function store()
    {
        if(isset($this->forms['title'])) {
            // 채팅방 코드 생성
            $hash = hash('sha256', $this->forms['title'] . date('Y-m-d H:i:s'));
            $code = substr($hash, 0, 8);
            $this->forms['code'] = $code;
            $this->forms['created_at'] = date('Y-m-d H:i:s');

            // 초대 코드 셍성
            $this->forms['invite'] = $hash;
            $this->forms['user_cnt'] = 1;

            // 코드 중복 체크
            $exists = DB::table('site_chat')
                ->where('code', $code)
                ->exists();
            if ($exists) {
                $this->message = "이미 존재하는 채팅방 코드입니다.";
                return;
            }

            // 채팅방 데이터 저장
            DB::table('site_chat')->insert($this->forms);
            $this->popupForm = false;
        }

        // 채팅방 데이터베이스 생성
        $path = database_path('chat');
        $code = $this->forms['code'];
        $path .= DIRECTORY_SEPARATOR.substr($code,0,2);
        $path .= DIRECTORY_SEPARATOR.substr($code,2,2);
        $path .= DIRECTORY_SEPARATOR.substr($code,4,2);
        if (!is_dir($path)) {
            // 해시코드를 이용한 서브 디렉토리 생성
            mkdir($path, 0755, true);
        }

        $dbfile = $path.DIRECTORY_SEPARATOR.$this->forms['code'].'.sqlite';
        if(file_exists($dbfile)) {
            // 이미 존재하는 경우
            return false;
        }

        //새로운 SQLite 파일 생성
        file_put_contents($dbfile, '');

        // 새로운 DB 연결 설정
        // Define a dynamic SQLite connection
        config(['database.connections.chat' => [
            'driver' => 'sqlite',
            'database' => $dbfile,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]]);

        $connection = DB::connection('chat');

        // 메세지 분할 테이블
        Schema::connection('chat')
        ->create('site_chat_block', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('year')->nullable();
            $table->string('month')->nullable();

        });

        // $year = date('Y');
        // $month = date('m');
        // $connection->table('site_chat_block')->insert([
        //     [
        //         'year' => $year,
        //         'month' => $month
        //     ]
        // ]);

        // Schema 클래스 사용을 위한 use 구문 추가 필요
        Schema::connection('chat')
        ->create('site_chat_message', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            //$table->string('partner_id')->nullable();
            //$table->string('partner_name')->nullable();
            $table->string('code')->nullable();

            $table->string('lang')->nullable();
            $table->text('message')->nullable();

            // 다국어
            $table->text('message_ko')->nullable();
            $table->text('message_en')->nullable();
            $table->text('message_ja')->nullable();
            $table->text('message_zh')->nullable(); // 중국어
            $table->text('message_fr')->nullable();
            $table->text('message_de')->nullable();

            $table->string('image')->nullable();
            $table->string('sender_id')->nullable();
            $table->string('receiver_id')->nullable();
            $table->enum('direction', ['send', 'receive'])->default('send');
            $table->timestamp('read_at')->nullable();

            $table->string('user_id')->nullable();
            $table->string('user_name')->nullable();

            // 읽음 표시
            $table->string('is_read')->nullable();

            // 관리자
            $table->string('manager')->nullable();
        });


        // 채팅방 목록에, 참여 맴버 추가
        // 개설한 사용자는 방장이다.
        DB::table('site_chat_room')
        ->insert([
            'code' => $this->forms['code'],

            'email' => $this->user_email,
            'user_id' => $this->user_id,
            'name' => $this->user_name,

            'lang' => 'ko',

            'is_owner' => true, // 방장 여부
        ]);

    }


    /**
     * 채팅방 수정
     */
    public function edit($id)
    {
        $row = DB::table('site_chat')
            ->where('id', $id)
            ->first();
        if($row) {
            $this->forms = get_object_vars($row); // 객체를 배열로 변환
        } else {
            $this->forms = [];
        }

        $this->popupEdit = true;
    }

    /**
     * 채팅방 수정 저장
     */
    public function update($id)
    {
        // 이미지 파일이 있는 경우
        if ($this->forms['image'] && is_object($this->forms['image'])) {
            $uploadFile = $this->forms['image'];

            // 임시 저장
            $tempPath = $uploadFile->store('temp', 'public');

            // public 경로 생성
            // 최종 저장 경로 생성
            $targetDir = public_path('images/chat');
            $code = $this->forms['code'];
            $path = DIRECTORY_SEPARATOR.substr($code,0,2);
            $path .= DIRECTORY_SEPARATOR.substr($code,2,2);
            $path .= DIRECTORY_SEPARATOR.substr($code,4,2);
            if (!is_dir($targetDir.$path)) {
                mkdir($targetDir.$path, 0777, true);
            }

            // 파일 이동
            $filename = basename($tempPath);
            $code = $this->forms['code'];
            $path = substr($code,0,2);
            $path .= '/'.substr($code,2,2);
            $path .= '/'.substr($code,4,2);
            $targetPath = 'images/chat/'.$path.'/'.$filename;
            rename(storage_path('app/public/'.$tempPath), public_path($targetPath));

            // 이미지 경로 저장
            $this->forms['image'] = "/".$targetPath;

            // 파일 초기화
            $uploadFile = null;
        }

        unset($this->forms['id']);
        $this->forms['updated_at'] = date('Y-m-d H:i:s');

        DB::table('site_chat')
            ->where('id', $id)
            ->update($this->forms);

        $this->popupEdit = false;
    }


    /**
     * 채팅방 삭제
     */
    public function delete($id)
    {
        $this->popupDelete = true;

        $this->forms['id'] = $id;
        $this->delete_password = '';
        $this->message = '';
    }

    /**
     * 채팅방 삭제 확인
     */
    public function deleteConfirm($id)
    {
        $row = DB::table('site_chat')->where('id', $id)->first();
        if(!$row->password) {
            // 비밀번호가 없는 경우
            $this->message = '삭제 비밀번호가 미설정 되어 있습니다.';
            return false;
        }

        if(!$this->delete_password) {
            // 비밀번호를 입력하지 않는 경우
            $this->message = '삭제 비밀번호를 입력하세요.';
            return false;
        }

        if($row->password != $this->delete_password) {
            // 비밀번호 불일치
            $this->message = '삭제 비밀번호가 일치하지 않습니다.';
            return false;
        }

        // 채팅방 데이터베이스 삭제
        // 채팅방 코드 조회
        $code = $row->code;
        if($code) {
            // sqlite 파일 삭제
            $path = database_path('chat');
            $path .= DIRECTORY_SEPARATOR.substr($code,0,2);
            $path .= DIRECTORY_SEPARATOR.substr($code,2,2);
            $path .= DIRECTORY_SEPARATOR.substr($code,4,2);

            $dbfile = $path.'/'.$code.'.sqlite';
            if(file_exists($dbfile)) {
                unlink($dbfile);
            }
        }

        // 이미지 삭제
        if($row->image) {
            $path = public_path($row->image);
            if(file_exists($path)) {
                unlink($path);
            }
        }

        // 사용자 삭제
        DB::table('site_chat_room')->where('code', $code)->delete();

        // 채팅방 삭제
        DB::table('site_chat')->where('id', $id)->delete();

        $this->popupDelete = false;
    }
}
