<div class="container">
    <x-wire-dialog-modal wire:model="popupEdit" :maxWidth="$popupWindowWidth">
        <x-slot name="title">
            {{ __('채팅방 설정') }}
        </x-slot>

        <x-slot name="content">
            @includeIf('jiny-site-chat::site.chat.forms')
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end space-x-2">
                <button wire:click="$set('popupEdit', false)" class="btn btn-secondary">
                    닫기
                </button>
                <button wire:click="update('{{ $room->id }}')" class="btn btn-primary">
                    수정
                </button>
            </div>
        </x-slot>
    </x-wire-dialog-modal>
</div>
