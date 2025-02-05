<div>
    {{-- 페이지네이션 및 검색 --}}
    <div class="row mb-3 align-items-center">
        <div class="col-12 col-md-4">
            <div class="d-flex gap-2">
                <input type="text" class="form-control "
                wire:model.defer="search_keyword"
                placeholder="검색어를 입력해 주세요">

                @if($search_keyword)
                <button class="btn btn-outline-secondary" wire:click="$set('search_keyword', '')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                    </svg>
                </button>
                @endif
            </div>
        </div>
        <div class="col-12 col-md-4 text-center">
            @if($rows->lastPage() > 1)
                <div class="pagination">
                    {{-- 이전 페이지 --}}
                    @if($rows->currentPage() > 1)
                        @if($rows->currentPage() > 10)
                            <a href="/home/chat/message/{{ $code }}?page={{ floor(($rows->currentPage()-1)/10) * 10 }}" class="btn btn-sm btn-outline-secondary">
                                이전
                            </a>
                        @else
                            {{-- <a href="/home/chat/message/{{ $code }}?page={{ $rows->currentPage()-1 }}" class="btn btn-sm btn-outline-secondary">
                                이전
                            </a> --}}
                        @endif
                    @endif

                    {{-- 페이지 번호 --}}
                    @for($i = 1; $i <= $rows->lastPage(); $i++)
                        <a href="/home/chat/message/{{ $code }}?page={{ $i }}"
                            class="btn btn-sm {{ $rows->currentPage() == $i ? 'btn-primary' : 'btn-outline-secondary' }}">
                            {{ $i }}
                        </a>
                    @endfor

                    {{-- 다음 페이지 --}}
                    @if($rows->currentPage() < $rows->lastPage())
                        @if($rows->lastPage() > 10)
                            <a href="/home/chat/message/{{ $code }}?page={{ $rows->currentPage()+1 }}"
                                class="btn btn-sm btn-outline-secondary">
                                다음
                            </a>
                        @else
                            {{-- <a href="/home/chat/message/{{ $code }}?page={{ $rows->currentPage()+1 }}"
                                class="btn btn-sm btn-outline-secondary">
                                다음
                            </a> --}}
                        @endif
                    @endif
                </div>
            @endif
        </div>
        <div class="col-12 col-md-4 text-end">
            전체 {{ $rows->total() }}개 메시지
        </div>
    </div>

    {{-- {{ $poll }} / {{ $poll_cnt }} --}}

    <div wire:poll.{{ $poll }}s="refreshData">
    </div>

    {{-- 메시지 pull  --}}
    <div >

        <div class="flex flex-col space-y-4 p-4 mb-4 rounded
            {{ isset($chat['bg_color']) ? $chat['bg_color'] : 'bg-gray-50' }}">
            @foreach ($rows->reverse() as $msg)
                {{-- 메시지 방향, 내글 오른쪽 배치 --}}
                <div class="flex {{ $msg->user_id == auth()->id() ? 'justify-end' : 'justify-start' }}">

                    {{-- 메시지 내용 --}}
                    <div class="d-flex flex-column">
                        {{-- 이미지 출력 --}}
                        @if ($msg->image)
                            <img src="/home/chat/message/{{ $code }}/{{ $msg->image }}"
                                alt="첨부 이미지"
                                class="max-w-full h-auto rounded">

                        {{-- 링크 출력 --}}
                        @elseif(filter_var($msg->message, FILTER_VALIDATE_URL))
                        <a href="{{ $msg->message }}" target="_blank">
                            <div class="p-2">
                                @php
                                    $url = $msg->message;
                                    $meta = get_meta_tags($url);
                                @endphp

                                @if(isset($meta['og:image']))
                                    <img src="{{ $meta['og:image'] }}" alt="링크 미리보기" class="w-full h-32 object-cover rounded mb-2">
                                @endif

                                <p class="text-sm text-blue-600 hover:underline">{{ $msg->message }}</p>

                                @if(isset($meta['description']))
                                    <p class="text-xs text-gray-600 mt-1">{{ $meta['description'] }}</p>
                                @endif
                            </div>
                        </a>
                        {{-- 텍스트 출력 --}}
                        @else
                            @if ($msg->user_id == auth()->id())
                                <div class="text-sm rounded p-2 bg-yellow-100">
                                    {!! nl2br(e($msg->message)) !!}
                                </div>
                            @else
                                <div class="d-flex rounded p-2 gap-2 bg-blue-200">
                                    <div class="text-sm">
                                        {!! nl2br(e($msg->message)) !!}
                                    </div>
                                    @if($msg->lang != $lang)
                                    <div class="text-sm" style="border-left: 1px solid #000; padding-left: 10px;">
                                        {{-- {!! nl2br(e($msg->message_ko)) !!} --}}
                                        {{ chatTranslateTo($msg, $lang, $chat['salt']) }}
                                    </div>
                                    @endif
                                </div>
                            @endif
                        @endif

                        {{-- 메시지 정보 --}}
                        <div class="d-flex gap-2  align-items-center {{ $msg->user_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">



                            @if ($msg->image)
                            <a href="{{ $msg->image }}" download class="text-xs text-gray-500 mt-1">
                                다운로드
                                </a>
                            @endif

                            {{-- 메시지 시간 --}}
                            <div class="text-xs text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($msg->created_at)->format('Y-m-d H:i') }}
                            </div>

                            {{-- 삭제버튼 --}}
                            @if ($msg->user_id == auth()->id())
                                <span style="" wire:click="deleteMessage({{ $msg->id }})"
                                    class="text-red-500 hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </span>
                            @endif
                        </div>

                    </div>

                    {{-- 아바타 이미지 --}}
                    <div class="d-flex flex-column">
                        @if ($msg->user_id != auth()->id())

                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center ml-2">
                            @if ($msg->user_id)
                                <img src="/home/avatas/{{ $msg->user_id }}" alt="User Avatar"
                                    class="w-8 h-8 rounded-full">
                            @else
                                <div class="w-4 h-4 bg-gray-500 rounded-full"></div>
                            @endif
                        </div>
                        @endif

                        {{-- 읽음 표시 --}}
                        @if ($msg->is_read)
                        <span class="text-xs text-gray-500 w-6 mt-1 ml-2">
                                @php
                                    $rcount = count(explode(',', $msg->is_read));
                                    $count = count($chat_members);
                                @endphp
                                @if($count > $rcount)
                                    {{ $count-$rcount }}
                                @else
                                읽음
                                @endif
                            </span>
                        @endif
                    </div>


                </div>
            @endforeach
        </div>
    </div>


    {{-- 메시지 입력 --}}
    <div class="flex input-group">
        <textarea class="form-control"
            wire:model.defer="messageText"
            placeholder="메시지를 입력하세요..."
            @keydown.enter.prevent="!$event.altKey && $wire.sendMessage()"
            @keydown.alt.enter="$event.target.value += '\n'"></textarea>
        <button class="btn btn-primary" wire:click="sendMessage">전송</button>
    </div>

    {{-- 파일 업로드 --}}
    <div class="flex justify-between items-center mt-2">
        <div>
            <label for="file-upload" class="cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 hover:text-gray-700" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
            </label>
            <input id="file-upload" type="file" class="hidden" wire:model="uploadFile"
                wire:change="$dispatch('file-chosen')">
            @if ($uploadFile)
                <span class="ml-2 text-sm text-gray-600">
                    {{ $uploadFile->getClientOriginalName() }}
                </span>
                <button class="btn btn-primary btn-sm ml-2" wire:click="uploadMessage">
                    파일 업로드
                </button>
            @endif

            <script>
                document.addEventListener('livewire:load', function() {
                    Livewire.on('file-chosen', () => {
                        let fileInput = document.getElementById('file-upload');
                        if (fileInput.files.length > 0) {
                            @this.upload('uploadFile', fileInput.files[0],
                                (uploadedFilename) => {
                                    // 업로드 완료
                                }, () => {
                                    // 업로드 진행률
                                }, (error) => {
                                    console.error('파일 업로드 실패:', error);
                                }
                            )
                        }
                    })
                });
            </script>

        </div>
        <div class="d-flex gap-1">
            <button onclick="copyCode('{{ $chat['invite'] }}')" class="btn btn-secondary btn-sm gap-2 mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16">
                    <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"/>
                    <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"/>
                </svg>
                초대코드 복사
            </button>

            <div class="dropdown">
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Settings
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="javascript:void(0)"
                            onclick="copyCode('{{ $chat['invite'] }}')">초대코드</a></li>
                    <li>
                        <a class="dropdown-item text-danger" href="javascript:void(0)" wire:click="exit">나가기</a>
                    </li>
                </ul>
            </div>

            <script>
                function copyCode(code) {
                    navigator.clipboard.writeText('/home/chat/invite/' + code);
                    alert('초대 코드가 복사되었습니다.');
                }
            </script>

        </div>
    </div>

</div>
