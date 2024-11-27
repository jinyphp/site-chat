<div>
    @includeIf('jiny-site-chat::site.chat.pagination')

    {{-- 60초마다 채팅방 목록 확인 --}}
    <div wire:poll.60s>
        <div class="row">
            @foreach ($rooms as $room)
                @includeIf('jiny-site-chat::site.chat.item')
            @endforeach
        </div>
    </div>

    @if ($popupEdit)
        @includeIf('jiny-site-chat::site.chat.edit')
    @endif

    @if ($popupDelete)
        @includeIf('jiny-site-chat::site.chat.delete')
    @endif
</div>
