<div class="container">
    <x-wire-dialog-modal wire:model="popupEdit" :maxWidth="$popupWindowWidth">
        <x-slot name="title">
            {{ __('채팅방 설정') }}
        </x-slot>

        <x-slot name="content">
            <div class="mb-3">
                <label for="title" class="form-label">채팅방 제목</label>
                <input type="text" id="title" class="form-control"
                    wire:model.defer="forms.title">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">접속 페스워드</label>
                <input type="password" id="password" class="form-control"
                    wire:model.defer="forms.password">
            </div>

            <div class="mb-3">
                <label for="imageUpload" class="form-label">채팅방 대표 이미지</label>
                <div wire:loading wire:target="forms.image" class="inline-flex items-center ml-2">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">업로드 중...</span>
                    </div>
                    <span class="text-primary ms-2">파일 업로드 중...</span>
                </div>
                <input type="file" class="form-control" id="imageUpload" wire:model.live="forms.image"
                    accept="image/*" wire:loading.attr="disabled">
                @if (isset($forms['image']))
                    <div class="">
                        {{ $forms['image'] }}
                    </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">채팅방 설명</label>
                <textarea id="description" class="form-control" wire:model.defer="forms.description"></textarea>
            </div>
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
