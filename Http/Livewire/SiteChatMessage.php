<?php
namespace Jiny\Site\Chat\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Livewire\Attributes\On;

class SiteChatMessage extends Component
{
    public $code;
    use WithFileUploads;

    public $search_keyword;

    //public $partner_id;
    public $user_id;
    public $user_name;
    public $user_email;

    public $messageText;
    public $direction = 'send';
    public $uploadFile;
    public $dbfile;

    public $chat;

    public $years = [];
    public $selectYear;

    public $poll = 5;
    public $poll_cnt = 0;
    public $lastUpdate;

    public $lang = 'ko';

    public $language = [];
    public $chat_members = [];

    public $viewFile;

    public function mount()
    {
        $this->partner_id = request()->id;

        $user = Auth::user();
        $this->user_name = $user->name;
        $this->user_email = $user->email;
        $this->user_id = $user->id;

        // 새로운 DB 연결 설정
        // Define a dynamic SQLite connection
        $path = database_path('chat');
        $path .= DIRECTORY_SEPARATOR.substr($this->code,0,2);
        $path .= DIRECTORY_SEPARATOR.substr($this->code,2,2);
        $path .= DIRECTORY_SEPARATOR.substr($this->code,4,2);
        $this->dbfile = $path.DIRECTORY_SEPARATOR.$this->code.'.sqlite';
        //dd($this->dbfile);

        // 채팅방 정보 조회
        $chat = DB::table('site_chat')
            ->where('code', $this->code)
            ->first();
        $this->chat = get_object_vars($chat); // 객체를 배열로 변환


        $years = $this->db('chat')->table('site_chat_block')->get();
        $this->years = [];
        $this->selectYear = date('Y');
        foreach($years as $item) {
            $this->years[] = $item->year;
        }
        //   $this->years = get_object_vars($years); // 객체를 배열로 변환


        // 채팅방 메시지 확인
        DB::table('site_chat_room')
            ->where('code', $this->code)
            ->where('email', $this->user_email)
            ->update(['last_checked_at' => date('Y-m-d H:i:s')]);

        // 채팅방 참여자 조회
        $this->getChatMember();
        //dd($this->chat_members);
        $this->lang = $this->myLanguage();
        //dd($this->lang);

        // 다국어 컬럼 체크
        $this->checkMessageColumn();

        if(!$this->viewFile) {
            $this->viewFile = 'jiny-site-chat::site.chat_message.message';
        }




    }

    private function getChatMember()
    {
        $members = DB::table('site_chat_room')
            ->where('code', $this->code)
            ->get();
        $this->chat_members = [];
        foreach($members as $item) {
            $this->chat_members[] = get_object_vars($item);
        }

        //dd($this->chat_members);
    }

    private function myLanguage($default='ko')
    {
        //dd($this->chat_members);
        foreach($this->chat_members as $item) {
            if($item['email'] == $this->user_email) {
                if($item['lang']) {
                    return $item['lang'];
                }
            }
        }

        return $default;
    }

    private function checkMessageColumn()
    {
        // 채팅방 참여자 언어 조회
        $language = DB::table('site_chat_room')
            ->where('code', $this->code)
            ->select('lang')
            ->groupBy('lang')
            ->get();
        $this->language = [];
        foreach($language as $item) {
            $this->language[] = get_object_vars($item);
        }

        // 다국어 컬럼
        $tableName = 'site_chat_message'; // 테이블 이름을 지정하세요.
        $columns = $this->db('chat')
        ->select("PRAGMA table_info($tableName)");

        foreach($this->language as $item) {
            $colName = 'message_'.$item['lang'];
            $hasMessageColumn = false;
            foreach($columns as $column) {
                if($column->name == $colName) {
                    $hasMessageColumn = true;
                    break;
                }
            }
            if(!$hasMessageColumn) {
                $this->db('chat')->statement("
                    ALTER TABLE site_chat_message
                    ADD COLUMN ".$colName." TEXT NULL
                ");
                //dd("컬럼이 추가되었습니다.");
            } else {
                //dd("컬럼이 이미 존재합니다.");
            }
        }
    }


    private function db($name)
    {
        config(['database.connections.'.$name => [
            'driver' => 'sqlite',
            'database' => $this->dbfile,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]]);

        return DB::connection($name);
    }

    /**
     * 채팅방 참여자 확인
     */
    private function isChatUser()
    {
        foreach($this->chat_members as $item) {
            if($item['email'] == $this->user_email) {
                return true;
            }
        }

        return false;
        // //dd($this->user_email);
        // //dd(Auth::user()->email);
        // $isParticipant = DB::table('site_chat_room')
        //     ->where('code', $this->code)
        //     ->where('email', Auth::user()->email)
        //     ->exists();

        // return $isParticipant;
    }

    public function render()
    {
        // 채팅방 참여자 확인
        if (!$this->isChatUser()) {
            return view('jiny-site-chat::site.chat_message.unauthorized');
        }

        $rows = $this->getMessage();
        $rows = $this->checkReadMember($rows); // 읽음 표시

        foreach($rows as &$item) {
            $item->message = chatMessageDecrypt($item->message, $this->chat['salt']);
        }

        return view($this->viewFile,[
            'rows' => $rows,
        ]);
    }

    #[On('refresh-user')]
    public function refreshUser()
    {
        $this->getChatMember();
        $this->lang = $this->myLanguage();
    }

    public function refreshData()
    {
        // $this->poll_cnt++;
        // if($this->poll_cnt > 3) {
        //     $this->poll_cnt = 0;
        //     $this->poll +=5;
        // }
    }

    private function checkReadMember($rows)
    {
        $updateIds = [];
        foreach($rows as &$item) {
            // is_read에 $this->user_id가 없는 경우 추가
            if ($item->is_read) {
                $readUsers = explode(',', $item->is_read);
                if (!in_array($this->user_id, $readUsers)) {
                    $readUsers[] = $this->user_id;
                    $item->is_read = implode(',', $readUsers);
                    $updateIds[] = $item->id;
                }
            }
        }

        // 읽음 표시 갱신
        if(!empty($updateIds)) {
            $db = $this->db('chat');
            $updateData = [];
            foreach($updateIds as $id) {
                $updateData[] = [
                    'id' => $id,
                    'is_read' => $rows->firstWhere('id', $id)->is_read
                ];
            }

            $db->table('site_chat_message')
                ->upsert($updateData, ['id'], ['is_read']);
        }

        return $rows;
    }

    private function getMessage()
    {
        $db  = $this->db('chat')->table('site_chat_message');
        if($this->search_keyword) {
            $db->where('message', 'like', '%'.$this->search_keyword.'%');
        }
        $rows = $db->orderBy('created_at', 'desc')
            ->paginate(10);

        return $rows;
    }


    /**
     * 메시지 전송
     */
    public function sendMessage()
    {
        // 메시지 내용이 비어있는지 확인
        if (empty($this->messageText)) {
            return;
        }

        // 메시지 암호화를 위한 salt 값 생성
        //$salt = "jiny-chat-salt";

        // 메시지 암호화
        $encryptedMessage = chatMessageEncrypt($this->messageText, $this->chat['salt']);

        // 새로운 메시지를 작성합니다.
        $db  = $this->db('chat')->table('site_chat_message');
        $id = $db->insertGetId([
            //'code' => $this->code,
            'lang' => $this->lang,

            'is_read' => $this->user_id,

            'user_id' => $this->user_id,
            'message' => $encryptedMessage,
            'direction' => $this->direction,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 마지막 메시지 시간 갱신
        DB::table('site_chat')
            ->where('code', $this->code)
            ->update(['last_message_at' => date('Y-m-d H:i:s')]);


        // 채팅방 확인 시간 갱신
        DB::table('site_chat_room')
            ->where('code', $this->code)
            ->where('email', $this->user_email)
            ->update(['last_checked_at' => date('Y-m-d H:i:s')]);


        // 메시지 입력창 초기화
        $this->messageText = '';
    }


    /**
     * 메시지 삭제
     */
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
                //'partner_id' => $this->partner_id,
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


    /**
     * 방 나가기
     */
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
    }


}
