<x-www-app>
    <x-www-layout>
        <x-www-main>
            <div class="row">

                <div class="col-12 col-md-3">
                    @livewire('site-chat-room', [
                        'viewFile' => 'jiny-site-chat::site.chat_message.room',
                        'code' => $code
                    ])
                </div>
                <div class="col-12 col-md-9">
                    @livewire('site-chat-message', ['code' => $code])
                    @livewire('site-chat-user', ['code' => $code])
                </div>
            </div>

        </x-www-main>
    </x-www-layout>
</x-www-app>

