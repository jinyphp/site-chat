<x-www-app>
    <x-www-layout>
        <x-www-main>

            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2>{{ $chat->title }}</h2>
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

