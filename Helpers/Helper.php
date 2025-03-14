<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Stichoza\GoogleTranslate\GoogleTranslate;


/**
 * 메시지 암호화
 */
function chatMessageEncrypt($message, $salt=null) {
    if(!$salt) {
        return $message;
    }

    // base64_encode와 str_rot13을 사용하여 간단한 암호화
    $encryptedMessage = base64_encode(
        str_rot13($message . $salt)
    );

    return $encryptedMessage;
}

/**
 * 메시지 복호화
 */
function chatMessageDecrypt($encryptedMessage, $salt=null) {
    if(!$salt) {
        return $encryptedMessage;
    }

    // base64_decode와 str_rot13을 사용하여 복호화
    $decryptedMessage = str_rot13(
        base64_decode($encryptedMessage)
    );

    // salt 값 제거
    $decryptedMessage = str_replace($salt, '', $decryptedMessage);

    return $decryptedMessage;
}



/**
 * 메시지 번역
 */
function chatTranslateTo($msg, $lang, $salt=null) {

    $column = 'message_'.$lang;
    if(isset($msg->$column)) {
        // 이미 번역된 데이터가 존재하는 경우
        $message = chatMessageDecrypt($msg->$column, $salt);
        return$message;
    }

    // 번역 데이터 DB 갱신
    return chatTranslateSave($msg, $lang, $salt);
}

/**
 * 구글 실시간 번역
 */
function translateByGoogle($msg, $lang) {
    if($msg->lang) {
        //dd($msg);
        $tr = new GoogleTranslate($msg->lang);
        $tr->setOptions([
            'verify' => false  // SSL 인증서 확인 비활성화
        ]);

        return $tr->setTarget($lang)->translate($msg->message);
    }

    return $msg->message;
}

function chatTranslateSave($msg, $lang, $salt=null) {
    $column = 'message_'.$lang;
    $code = $msg->code;

    // 구글 실시간 번역
    $message = translateByGoogle($msg, $lang);

    // 번역 데이터 DB 갱신
    $path = chatSqlitePath($code);
    config(['database.connections.chat' => [
        'driver' => 'sqlite',
        'database' => $path.DIRECTORY_SEPARATOR.$code.'.sqlite',
        'prefix' => '',
        'foreign_key_constraints' => true,
    ]]);


    // 암호화
    $encryptedMessage = chatMessageEncrypt($message, $salt);

    $connection = DB::connection('chat');
    $connection->table('site_chat_message')
        ->where('id', $msg->id)
        ->update([
            $column => $encryptedMessage
        ]);

    return $message;
}

function chatSqlitePath($code) {
    $path = database_path('chat');
    $path .= DIRECTORY_SEPARATOR.substr($code,0,2);
    $path .= DIRECTORY_SEPARATOR.substr($code,2,2);
    $path .= DIRECTORY_SEPARATOR.substr($code,4,2);
    return $path;
}




// 새로운 채팅방을 생성합니다.
function siteNewChat($title) {
    $hash = hash('sha256', $title . date('Y-m-d H:i:s'));
    $code = substr($hash, 0, 8);
    $created_at = date('Y-m-d H:i:s');

    // 초대 코드 셍성
    $invite = $hash;

    // 중복 코드 검사
    if (DB::table('site_chat')->where('code', $code)->exists()) {
        return false;
    }

    // 계시판 목록 추가
    DB::table('site_chat')->insert([
        'code' => $code,
        'title' => $title,
        'created_at' => $created_at,
        'invite' => $invite,
    ]);

    // sqlite 데이터베이스 생성
    siteMakeChatDB($code);

    return $code;
}

function siteMakeChatDB($code) {

    // 채팅방 데이터베이스 생성
    $path = database_path('chat');

    // 해시코드를 이용한 서브 디렉토리 생성
    $path .= DIRECTORY_SEPARATOR.substr($code,0,2);
    $path .= DIRECTORY_SEPARATOR.substr($code,2,2);
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }

    $dbfile = $path.DIRECTORY_SEPARATOR.$code.'.sqlite';
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
    Schema::connection('chat')->create('site_chat_message',
        function (Blueprint $table) {
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
    siteChatAddUser($code, Auth::user()->id, $is_owner=true);
    // DB::table('site_chat_room')->insert([
    //     'code' => $code,
    //     'email' => Auth::user()->email,
    //     'user_id' => Auth::user()->id,
    //     'is_owner' => true, // 방장 여부
    // ]);

    return $code;
}

function siteChatAddUser($code, $user_id, $is_owner=false) {

    $user = DB::table('users')->where('id', $user_id)->first();

    if($user) {
        // 맴버추가
        DB::table('site_chat_room')->insert([
            'code' => $code,
            'email' => $user->email,
            'user_id' => $user_id,
            'is_owner' => $is_owner, // 방장 여부
        ]);

        return true;
    }

    return false;
}
