<?php
namespace Jiny\Site\Chat\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

class SiteChatMessage extends Component
{
    public $code;
    use WithFileUploads;

    public $search_keyword;

    public $partner_id;
    public $user_id;
    public $messageText;
    public $direction = 'send';
    public $uploadFile;
    public $dbfile;

    public $chat;

    public $years = [];
    public $selectYear;

    public $poll = 5;
    public $lastUpdate;

    public $lang = 'ko';

    public $language = [];

    public function mount()
    {
        $this->partner_id = request()->id;
        $this->user_id = Auth::user()->id;

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

        $user = DB::table('site_chat_room')
            ->where('code', $this->code)
            ->where('email', Auth::user()->email)
            ->first();
        $this->lang = $user->lang;

        // 다국어 컬럼 체크
        $this->checkMessageColumn();
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
        $isParticipant = DB::table('site_chat_room')
            ->where('code', $this->code)
            ->where('email', Auth::user()->email)
            ->exists();

        return $isParticipant;
    }

    public function render()
    {
        // 채팅방 참여자 확인
        if (!$this->isChatUser()) {
            return view('jiny-site-chat::site.chat_message.unauthorized');
        }


        $db  = $this->db('chat')->table('site_chat_message');
        if($this->search_keyword) {
            $db->where('message', 'like', '%'.$this->search_keyword.'%');
        }
        $rows  = $db->orderBy('created_at', 'asc')
            ->paginate(10);

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
        $id = $db->insertGetId([
            //'code' => $this->code,
            'lang' => $this->lang,

            'user_id' => $this->user_id,
            'message' => $this->messageText,
            'direction' => $this->direction,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // // 다국어 컬럼
        // $tableName = 'site_chat_message'; // 테이블 이름을 지정하세요.
        // $columns = $this->db('chat')
        // ->select("PRAGMA table_info($tableName)");
        // $hasMessageKoColumn = false;
        // foreach($columns as $column) {
        //     if($column->name == 'message_ko') {
        //         $hasMessageKoColumn = true;
        //         break;
        //     }
        // }
        // if(!$hasMessageKoColumn) {
        //     $this->db('chat')->statement("
        //         ALTER TABLE site_chat_message
        //         ADD COLUMN message_ko TEXT NULL
        //     ");
        //     //dd("컬럼이 추가되었습니다.");
        // } else {
        //     //dd("컬럼이 이미 존재합니다.");
        // }

        // // 구글 번역 부분 수정
        // try {
        //     $tr = new GoogleTranslate('en'); // Translates into English
        //     $tr->setOptions([
        //         'verify' => false  // SSL 인증서 확인 비활성화
        //     ]);
        //     $messageKo = $tr->setTarget('ko')->translate('Goodbye');
        //     $db->where('id', $id)->update([
        //         'message_ko' => $messageKo
        //     ]);
        // } catch (\Exception $e) {
        //     // 번역 실패 시 원본 메시지 저장
        //     $db->where('id', $id)->update([
        //         'message_ko' => $this->messageText
        //     ]);
        // }

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
    }


}
