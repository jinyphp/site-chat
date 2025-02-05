<x-navtab class="mb-3 nav-bordered">

    <!-- formTab -->
    <x-navtab-item class="show active">
        <x-navtab-link class="rounded-0 active">
            <span class="d-none d-md-block">기본정보</span>
        </x-navtab-link>


        <div class="mb-3">
            <label for="title" class="form-label">채팅방 제목</label>
            <input type="text" id="title" class="form-control" wire:model.defer="forms.title">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">접속 페스워드</label>
            <input type="password" id="password" class="form-control" wire:model.defer="forms.password">
        </div>

        <div class="mb-3">
            <label for="imageUpload" class="form-label">채팅방 대표 이미지</label>
            <div wire:loading wire:target="forms.image" class="inline-flex items-center ml-2">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">업로드 중...</span>
                </div>
                <span class="text-primary ms-2">파일 업로드 중...</span>
            </div>
            <input type="file" class="form-control" id="imageUpload" wire:model.live="forms.image" accept="image/*"
                wire:loading.attr="disabled">
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


    </x-navtab-item>

    <!-- formTab -->
    <x-navtab-item class="">
        <x-navtab-link class="rounded-0">
            <span class="d-none d-md-block">보완</span>
        </x-navtab-link>

        <p>입력 메시지를 암호화 하여 DB에 저장됩니다. 메시지를 복호화 하기 위해서는 반드시 지정된 동일 암호가 필요합니다.</p>
        <div class="mb-3">
            <label for="salt" class="form-label">Salt</label>
            <input type="text" id="salt" class="form-control" wire:model.defer="forms.salt">
        </div>

    </x-navtab-item>
</x-navtab>
