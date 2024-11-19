<div wire:poll.5s>
    {{-- <h2>메시지 {{ $partner_id }}</h2> --}}
    <!-- 페이지네이션 -->

    <div class="d-flex justify-content-between align-items-center my-4">
        <div>
            전체 {{$rows->total()}}개의 메시지가 존재합니다.
        </div>
        <div>
            {{ $rows->onEachSide(2)->links('pagination::bootstrap-4') }}
        </div>

    </div>

    <div class="flex flex-col space-y-4 p-4 mb-4 {{ isset($chat['bg_color']) ? $chat['bg_color'] : 'bg-gray-50' }}">
        @foreach ($rows as $msg)
            <div class="flex {{ $msg->user_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                {{-- @if($msg->user_id == auth()->id())
                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center mr-2">
                        @if($msg->user_id)
                            <img src="/home/avatas/{{ $msg->user_id }}"
                                alt="User Avatar"
                                class="w-8 h-8 rounded-full">
                        @else
                            <div class="w-4 h-4 bg-gray-500 rounded-full"></div>
                        @endif
                    </div>
                @endif --}}

                @if($msg->image)
                    <div class="rounded-lg p-3 {{ $msg->user_id == auth()->id() ? ($chat['send_color'] ?? 'bg-yellow-100') : ($chat['receive_color'] ?? 'bg-blue-200') }} max-w-[70%] relative">
                        @if($msg->user_id == auth()->id())
                            <span style="position:absolute; right:5px; top:3px"
                                wire:click="deleteMessage({{ $msg->id }})"
                                class="text-red-500 hover:text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </span>
                        @endif

                        <img src="/home/chat/message/{{ $code }}/{{ $msg->image }}"
                            alt="첨부 이미지"
                            class="max-w-full h-auto rounded">

                        <p class="text-xs text-gray-500 mt-1">
                            {{ \Carbon\Carbon::parse($msg->created_at)
                            ->format('Y-m-d H:i') }}

                            <a href="{{ $msg->image }}"
                                download
                                class="mt-2">
                                다운로드
                            </a>
                        </p>

                    </div>
                    {{-- <p class="text-xs text-gray-500 mt-1">
                        {{ \Carbon\Carbon::parse($msg->created_at)
                        ->format('Y-m-d H:i') }}
                    </p> --}}

                @elseif(filter_var($msg->message, FILTER_VALIDATE_URL))

                <div class="rounded-lg p-3 {{ $msg->user_id == auth()->id() ? ($chat['send_color'] ?? 'bg-yellow-100') : ($chat['receive_color'] ?? 'bg-blue-200') }} max-w-[70%] relative">
                    @if($msg->user_id == auth()->id())
                        <span style="position:absolute; right:5px; top:3px"
                            wire:click="deleteMessage({{ $msg->id }})"
                            class="text-red-500 hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </span>
                    @endif

                    <a href="{{ $msg->message }}" target="_blank">

                        <div class="p-2">
                            <p class="text-sm text-blue-600 hover:underline">{{ $msg->message }}</p>
                        </div>
                    </a>

                    <p class="text-xs text-gray-500 mt-1">
                        {{ \Carbon\Carbon::parse($msg->created_at)
                        ->format('Y-m-d H:i') }}
                    </p>

                </div>

                    {{-- <div class="rounded-lg p-3 bg-gray-100 max-w-[70%] mt-2">


                        <div class="border rounded">

                        </div>
                    </div> --}}



                @else
                    <div class="rounded-lg p-3 {{ $msg->user_id == auth()->id() ? ($chat['send_color'] ?? 'bg-yellow-100') : ($chat['receive_color'] ?? 'bg-blue-200') }} max-w-[70%] relative">
                        @if($msg->user_id == auth()->id())
                            <span style="position:absolute; right:5px; top:3px"
                                wire:click="deleteMessage({{ $msg->id }})"
                                class="text-red-500 hover:text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </span>
                        @endif
                        <p class="text-sm">{!! nl2br(e($msg->message)) !!}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ \Carbon\Carbon::parse($msg->created_at)
                            ->format('Y-m-d H:i') }}
                        </p>

                    </div>
                    {{-- <p class="text-xs text-gray-500 mt-1">
                        {{ \Carbon\Carbon::parse($msg->created_at)
                        ->format('Y-m-d H:i') }}
                    </p> --}}
                @endif




                @if($msg->user_id != auth()->id())
                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center ml-2">
                        @if($msg->user_id)
                            <img src="/home/avatas/{{ $msg->user_id }}"
                                alt="User Avatar"
                                class="w-8 h-8 rounded-full">
                        @else
                            <div class="w-4 h-4 bg-gray-500 rounded-full"></div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- 메시지 입력 --}}
    <div class="flex input-group">
        <textarea class="form-control" wire:model.defer="messageText" placeholder="메시지를 입력하세요..."></textarea>
        <button class="btn btn-primary" wire:click="sendMessage">전송</button>
    </div>

    {{-- 파일 업로드 --}}
    <div class="flex items-center mt-2">
        <label for="file-upload" class="cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 hover:text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
            </svg>
        </label>
        <input id="file-upload"
            type="file"
            class="hidden"
            wire:model="uploadFile"
            wire:change="$dispatch('file-chosen')">
        @if($uploadFile)
            <span class="ml-2 text-sm text-gray-600">
                {{ $uploadFile->getClientOriginalName() }}
            </span>
            <button class="btn btn-primary btn-sm ml-2" wire:click="uploadMessage">
                파일 업로드
            </button>
        @endif

    </div>

    <script>
        document.addEventListener('livewire:load', function () {
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

    <!-- 페이지네이션 -->
    <div class="d-flex justify-content-between align-items-center my-4">

        <div>
            {{ $rows->onEachSide(2)->links('pagination::bootstrap-4') }}
        </div>

        <div>
            전체 {{$rows->total()}}개의 메시지가 존재합니다.
        </div>
    </div>

</div>
