<x-wire-dialog-modal wire:model="popupDelete" :maxWidth="$popupWindowWidth">
    <x-slot name="title">
        {{ __('채팅방 삭제') }}
    </x-slot>
    <x-slot name="content">
        <div class="mb-3">
            <label for="title" class="form-label">삭제 비밀번호</label>
            <input type="password" id="title" class="form-control"
                wire:model.defer="delete_password">
        </div>
        @if ($message)
            <div class="alert alert-danger">
                {{ $message }}
            </div>
        @endif
    </x-slot>
    <x-slot name="footer">
        <div class="flex justify-end space-x-2">
            <button wire:click="$set('popupDelete', false)" class="btn btn-secondary">
                닫기
            </button>
            <button wire:click="deleteConfirm('{{ $room->id }}')" class="btn btn-danger">
                삭제
            </button>
        </div>
    </x-slot>
</x-wire-dialog-modal>
