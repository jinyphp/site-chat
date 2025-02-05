<x-www-app>
    <x-www-layout>
        <x-www-main>

            <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-center gap-2">
                    @if($chat->salt)
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-database-lock" viewBox="0 0 16 16">
                        <path d="M13 5.698a5 5 0 0 1-.904.525C11.022 6.711 9.573 7 8 7s-3.022-.289-4.096-.777A5 5 0 0 1 3 5.698V7c0 .374.356.875 1.318 1.313C5.234 8.729 6.536 9 8 9c.666 0 1.298-.056 1.876-.156-.43.31-.804.693-1.102 1.132A12 12 0 0 1 8 10c-1.573 0-3.022-.289-4.096-.777A5 5 0 0 1 3 8.698V10c0 .374.356.875 1.318 1.313C5.234 11.729 6.536 12 8 12h.027a4.6 4.6 0 0 0-.017.8A2 2 0 0 0 8 13c-1.573 0-3.022-.289-4.096-.777A5 5 0 0 1 3 11.698V13c0 .374.356.875 1.318 1.313C5.234 14.729 6.536 15 8 15c0 .363.097.704.266.997Q8.134 16.001 8 16c-1.573 0-3.022-.289-4.096-.777C2.875 14.755 2 14.007 2 13V4c0-1.007.875-1.755 1.904-2.223C4.978 1.289 6.427 1 8 1s3.022.289 4.096.777C13.125 2.245 14 2.993 14 4v4.256a4.5 4.5 0 0 0-1.753-.249C12.787 7.654 13 7.289 13 7zm-8.682-3.01C3.356 3.124 3 3.625 3 4c0 .374.356.875 1.318 1.313C5.234 5.729 6.536 6 8 6s2.766-.27 3.682-.687C12.644 4.875 13 4.373 13 4c0-.374-.356-.875-1.318-1.313C10.766 2.271 9.464 2 8 2s-2.766.27-3.682.687Z"/>
                        <path d="M9 13a1 1 0 0 1 1-1v-1a2 2 0 1 1 4 0v1a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1zm3-3a1 1 0 0 0-1 1v1h2v-1a1 1 0 0 0-1-1"/>
                      </svg>
                    @endif

                    <h2 class="mb-0">{{ $chat->title }}</h2>

                    @if($chat->user_cnt > 1)
                        <span class="badge bg-primary">{{ $chat->user_cnt }}명</span>
                    @endif
                </div>
                <div>
                    <!-- Breadcrumb -->
                    <nav class="pb-2 pb-md-3" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="home-contractors.html">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chat</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <p>
                {{ $chat->description }}
            </p>


            <div class="row">

                <div class="col-12 col-md-3 order-2 order-md-1 ">
                    <h5 style="margin-bottom:1.8rem;">
                        <a href="/home/chat" style="text-decoration:none; color:inherit;">

                            <span>
                                채팅목록
                            </span>
                        </a>
                    </h5>

                    {{-- @livewire('site-chat-room', [
                        'viewFile' => 'jiny-site-chat::site.chat_message.room',
                        'code' => $code
                    ]) --}}
                    @livewire('site-chat-user', ['code' => $code])
                </div>
                <div class="col-12 col-md-9 order-1 order-md-2">
                    @livewire('site-chat-message', ['code' => $code])
                </div>
            </div>

        </x-www-main>
    </x-www-layout>
</x-www-app>

