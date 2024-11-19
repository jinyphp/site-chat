<?php
namespace Jiny\Site\Chat\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SiteChatMessage extends Component
{
    public $code;
    use WithFileUploads;

    public $partner_id;
    public $user_id;
    public $messageText;
    public $direction = 'send';
    public $uploadFile;
    public $dbfile;
    public $chat = [];

    public function mount()
    {
        $this->partner_id = request()->id;
        $this->user_id = Auth::user()->id;

        // 새로운 DB 연결 설정
        // Define a dynamic SQLite connection
        $path = database_path('chat');
        $path .= DIRECTORY_SEPARATOR.substr($this->code,0,2);
        $path .= DIRECTORY_SEPARATOR.substr($this->code,2,2);
        $this->dbfile = $path.DIRECTORY_SEPARATOR.$this->code.'.sqlite';
        //dd($this->dbfile);

        $chat = $this->db('chat')->table('site_chat_message')->get();
        $this->chat = get_object_vars($chat); // 객체를 배열로 변환
    }

    private function db($name)
    {
        // return DB::connection([
        //     'driver' => 'sqlite',
        //     'database' => $this->dbfile,
        //     'prefix' => '',
        //     'foreign_key_constraints' => true,
        // ]);

        config(['database.connections.'.$name => [
            'driver' => 'sqlite',
            'database' => $this->dbfile,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]]);

        return DB::connection($name);
    }

    public function render()
    {
        // 채팅방 참여자 확인
        $isParticipant = DB::table('site_chat_room')
            ->where('code', $this->code)
            ->where('email', Auth::user()->email)
            ->exists();

        if (!$isParticipant) {
            return view('jiny-site-chat::site.chat_message.unauthorized');
        }


        $db  = $this->db('chat')->table('site_chat_message');
        // $rows  = $db->where('partner_id', $this->partner_id)
        //     ->where('user_id', $this->user_id)
        //     ->orderBy('created_at', 'asc')
        //     ->paginate(10);
        $rows  = $db->orderBy('created_at', 'asc')
            ->paginate(10);

        // if($this->direction == 'send') {
        //     $viewFile = 'jiny-site-chat::site.chat_message.send';
        // } else {
        //     $viewFile = 'jiny-site-chat::site.chat_message.receive';
        // }

        $viewFile = 'jiny-site-chat::site.chat_message.message';

        return view($viewFile,[
            'rows' => $rows,
        ]);
    }

    public function sendMessage()
    {
        // 메시지 내용이 비어있는지 확인
        if (empty($this->messageText)) {
            return;
        }

        // 메시지
        $db  = $this->db('chat')->table('site_chat_message');
        $db->insert([
            'partner_id' => $this->partner_id,
            'user_id' => $this->user_id,
            'message' => $this->messageText,
            'direction' => $this->direction,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 메시지 입력창 초기화
        $this->messageText = '';
    }



    public function deleteMessage($id)
    {
        // 이미지 파일이 있는지 확인
        $db  = $this->db('chat')->table('site_chat_message');
        $message = $db->where('id', $id)->first();
        if ($message && $message->image) {
            // 이미지 파일 경로에서 앞의 / 제거
            $imagePath = ltrim($message->image, '/');

            // 이미지 파일 삭제
            if (file_exists(public_path($imagePath))) {
                unlink(public_path($imagePath));
            }
        }

        // DB에서 메시지 삭제
        $db  = $this->db('chat')->table('site_chat_message');
        $db->where('id', $id)->delete();
    }

    public function uploadMessage()
    {
        if ($this->uploadFile) {
            // 임시 저장
            $tempPath = $this->uploadFile->store('temp', 'public');
            $path = $this->uploadMoveFile($tempPath);

            // 메시지 저장
            $db  = $this->db('chat')->table('site_chat_message');
            $db->insert([
                'partner_id' => $this->partner_id,
                'user_id' => $this->user_id,
                'image' => $path, // 이미지 파일 경로 저장
                'direction' => $this->direction,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 파일 초기화
            $this->uploadFile = null;
        }
    }

    /**
     * storage_path 에 저장
     */
    private function uploadMoveFile($tempPath)
    {
        // 파일 저장 경로
        $path = storage_path('app/public/chat');
        $path .= DIRECTORY_SEPARATOR.substr($this->code,0,2);
        $path .= DIRECTORY_SEPARATOR.substr($this->code,2,2);
        $path .= DIRECTORY_SEPARATOR.$this->code;

        if(!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        // $path = "/images/chat/";
        // $path .= DIRECTORY_SEPARATOR.substr($this->code,0,2);
        // $path .= DIRECTORY_SEPARATOR.substr($this->code,2,2);
        // // 최종 저장 경로 생성
        // $targetDir = public_path($path.$this->code);
        // if (!file_exists($targetDir)) {
        //     mkdir($targetDir, 0777, true);
        // }

        // 파일 이동
        $filename = basename($tempPath);
        $targetPath = $path.DIRECTORY_SEPARATOR.$filename;
        rename(storage_path('app/public/'.$tempPath), $targetPath);

        return $filename;
        //return $targetPath;
    }



}
