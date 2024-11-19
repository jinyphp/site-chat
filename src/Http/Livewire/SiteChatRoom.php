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

class SiteChatRoom extends Component
{
    use WithFileUploads;

    public $code;

    //public $partners;
    public $popupForm = false;
    public $popupEdit = false;
    public $popupWindowWidth = '2xl';
    public $forms = [];
    public $viewFile;

    //public $permitEdit = false;

    public function mount()
    {
        if(!$this->viewFile) {
            $this->viewFile = 'jiny-site-chat::site.chat.room';
        }
    }

    public function render()
    {
        //$rows = DB::table('site_chat')->paginate(8);
        $rows = DB::table('site_chat')
            ->join('site_chat_room', 'site_chat.code', '=', 'site_chat_room.code')
            ->where('site_chat_room.email', Auth::user()->email)
            ->select('site_chat.*')
            ->paginate(8);


        $rooms = DB::table('site_chat_room')
            ->where('email', Auth::user()->email)
            ->get();
        $rooms = $rooms->keyBy('code');


        foreach($rows as &$row) {
            $code = $row->code;
            $row->is_owner = $rooms[$code]->is_owner;
        }

        return view($this->viewFile,[
            'rooms' => $rows
        ]);
    }

    public function create()
    {
        $this->popupForm = true;
        $this->forms = [];
    }

    public function store()
    {
        if(isset($this->forms['title'])) {
            $hash = hash('sha256', $this->forms['title'] . date('Y-m-d H:i:s'));
            $this->forms['code'] = substr($hash, 0, 8);
            $this->forms['created_at'] = date('Y-m-d H:i:s');

            // 초대 코드 셍성
            $this->forms['invite'] = $hash;

            DB::table('site_chat')->insert($this->forms);
            $this->popupForm = false;
        }

        // 채팅방 데이터베이스 생성
        $path = database_path('chat');
        // 해시코드를 이용한 서브 디렉토리 생성
        $code = $this->forms['code'];
        $path .= DIRECTORY_SEPARATOR.substr($code,0,2);
        $path .= DIRECTORY_SEPARATOR.substr($code,2,2);
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $dbfile = $path.DIRECTORY_SEPARATOR.$this->forms['code'].'.sqlite';
        if(file_exists($dbfile)) {
            // 이미 존재하는 경우
            return false;
        }

        //touch($path);
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

        $year = date('Y');
        $month = date('m');
        $connection->table('site_chat_block')->insert([
            [
                'year' => $year,
                'month' => $month
            ]
        ]);

        // Schema 클래스 사용을 위한 use 구문 추가 필요
        Schema::connection('chat')->create('site_chat_message', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('partner_id')->nullable();
            $table->string('partner_name')->nullable();

            $table->text('message')->nullable();
            $table->string('image')->nullable();
            $table->string('sender_id')->nullable();
            $table->string('receiver_id')->nullable();
            $table->enum('direction', ['send', 'receive'])->default('send');
            $table->timestamp('read_at')->nullable();

            $table->string('user_id')->nullable();
            $table->string('user_name')->nullable();

            $table->string('manager')->nullable();
        });

        // 맴버추가
        DB::table('site_chat_room')->insert([
            'code' => $this->forms['code'],
            'email' => Auth::user()->email,
            'user_id' => Auth::user()->id,
            'is_owner' => true, // 방장 여부
        ]);

    }

    public function delete($id)
    {
        DB::table('site_chat')->where('id', $id)->delete();

        // 채팅방 삭제
        // 채팅방 코드 조회
        $code = DB::table('site_chat')->where('id', $id)->value('code');
        if($code) {
            // sqlite 파일 삭제
            $path = database_path('chat');
            $path .= DIRECTORY_SEPARATOR.substr($code,0,2);
            $path .= DIRECTORY_SEPARATOR.substr($code,2,2);

            $dbfile = $path.'/'.$code.'.sqlite';
            if(file_exists($dbfile)) {
                unlink($dbfile);
            }
        }
    }

    public function edit($id)
    {
        $this->popupEdit = true;

        $row = DB::table('site_chat')->where('id', $id)->first();
        $this->forms = get_object_vars($row); // 객체를 배열로 변환
    }

    public function update($id)
    {
        // 이미지 파일이 있는 경우
        if ($this->forms['image'] && is_object($this->forms['image'])) {
            $uploadFile = $this->forms['image'];

            // 임시 저장
            $tempPath = $uploadFile->store('temp', 'public');

            // 최종 저장 경로 생성
            $targetDir = public_path('images/chat');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // 파일 이동
            $filename = basename($tempPath);
            $targetPath = 'images/chat/'.$filename;
            rename(storage_path('app/public/'.$tempPath), public_path($targetPath));

            // 이미지 경로 저장
            $this->forms['image'] = "/".$targetPath;

            // 파일 초기화
            $uploadFile = null;
        }

        DB::table('site_chat')->where('id', $id)->update($this->forms);
        $this->popupEdit = false;
    }
}
