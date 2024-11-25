<div>
    <div class="d-flex justify-content-between mb-4">
        <div>
            전체 {{ $rooms->total() }}개
        </div>
        <div>
            {{ $rooms->links() }}
        </div>
        <div>
            @if ($popupForm)
                <div class="input-group">
                    <input type="text" class="form-control" wire:model="forms.title">
                    <button class="btn btn-primary" wire:click="store">생성</button>
                </div>
            @else
                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary" wire:click="create">채팅방 생성</button>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        @foreach ($rooms as $room)
            <div class="col-md-6 col-12 mb-4">
                <div class="card bg-gray-100 border-0">
                    <div class="row g-0">
                        <div class="col-4">
                            @if ($room->image)
                                <a href="/home/chat/message/{{ $room->code }}">
                                    <img src="{{ $room->image }}" alt="{{ $room->title }}"
                                        class="img-fluid rounded-start h-100 object-fit-cover">
                                </a>
                            @else
                                <a href="/home/chat/message/{{ $room->code }}">
                                    <div class="bg-gray-200 w-100 h-100 rounded-start"></div>
                                </a>
                            @endif
                        </div>
                        <div class="col-8">
                            <div class="card-body">
                                <div class="fs-xs text-body-secondary me-3">{{ $room->created_at }}</div>
                                <h3 class="h5 mb-2">
                                    <a class=""
                                        href="/home/chat/message/{{ $room->code }}">{{ $room->title }}</a>
                                </h3>
                                <p class="fs-sm pt-2 mt-1 mb-0">
                                    {{ $room->description }}
                                </p>

                                <div class="mt-2">
                                    @if ($room->is_owner)
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            wire:click="delete('{{ $room->id }}')">
                                            삭제
                                        </button>


                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            wire:click="edit('{{ $room->id }}')">
                                            수정
                                        </button>
                                    @endif

                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="navigator.clipboard.writeText('/home/chat/invite/{{ $room->invite }}')">
                                        초대코드 복사
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>





    @if ($popupEdit)
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
    @endif

</div>
