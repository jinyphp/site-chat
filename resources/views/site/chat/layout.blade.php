<x-www-app>
    <x-www-layout>
        <section class="py-5 bg-light mb-4">
            <div class="container">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h2>채팅 목록</h2>
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
                    채팅방 기능을 통하여 간단한 소통을 할 수 있습니다.
                    채팅 메시지 로그는 분산된 데이터베이스에 기록됩니다.
                </p>
            </div>
        </section>

        <x-www-main>
            @livewire('site-chat-room')

            <hr>

        </x-www-main>

    </x-www-layout>
</x-www-app>
