<?php
namespace Jiny\Site\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AssetsController extends Controller
{
    public function __construct()
    {
        // 라이센스 키 설정
        $this->licenseKey = "chat";
    }


    public function index(Request $request)
    {
        $code = $request->code;
        $file = $request->file;

        // 채팅방 참여자만 이미지에 접근할 수 있습니다.
        $isParticipant = DB::table('site_chat_room')
            ->where('code', $code)
            ->where('email', Auth::user()->email)
            ->exists();

        if (!$isParticipant) {
            abort(404, '파일을 찾을 수 없습니다.');
            //return view('jiny-site-chat::site.chat_message.unauthorized');
        }

        // 파일 경로
        //$path = resource_path('chat');
        $path = storage_path('app/public/chat');
        $path .= DIRECTORY_SEPARATOR.substr($code,0,2);
        $path .= DIRECTORY_SEPARATOR.substr($code,2,2);
        $path .= DIRECTORY_SEPARATOR.$code;

        $filename = $path.DIRECTORY_SEPARATOR.$file;

        if (file_exists($filename   )) {
            return $this->response($filename);
        }

        // 파일이 없습니다.
        abort(404, '파일을 찾을 수 없습니다.');
    }

    private function response($file)
    {
        // 파일 이름에서 확장자 추출
        $mime = $this->contentType($file);

        // BinaryFileResponse 인스턴스 생성
        $response = new BinaryFileResponse($file);

        // Content-Type 헤더 설정
        $response->headers->set('Content-Type', $mime);
        return $response;
    }

    private function contentType($file)
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        switch( $extension ) {
            case "css":
                // CSS 파일인 경우
                $mime="text/css";
                break;
            case "js":
                // 예를 들어, JavaScript 파일인 경우
                $mime="application/javascript";
                break;

            case "json":
                $mime="application/json";
                break;

            case "gif":
                $mime="image/gif";
                break;
            case "png":
                $mime="image/png";
                break;
            case "jpeg":
            case "jpg":
                $mime="image/jpeg";
                break;
            case "svg":
                $mime="image/svg+xml";
                break;
            default:
                // 기본적으로 알려진 MIME 유형이 없는 경우
                $mime="application/octet-stream";
        }

        return $mime;
    }
}
