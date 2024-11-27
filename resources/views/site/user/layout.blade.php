<div>
    {{-- 맴버 목록 --}}
    <ul class="list-group">
        @foreach ($rows as $row)
            <li class="list-group-item" wire:click="edit({{ $row->id }})">
                <div class="flex items-center p-2">
                    <div>
                        @if ($row->user_id)
                        <img src="/home/avatas/{{ $row->user_id }}" alt="User Avatar" class="w-8 h-8 rounded-full">
                        @else
                            <span class="w-8 h-8 bg-gray-300 rounded-full" style="display:inline-block"></span>
                        @endif
                    </div>

                    <div>
                        <span class="ml-2">
                            {{ $row->email }}
                        </span>

                        <span class="ml-2">
                            ({{ $row->lang }})
                        </span>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>



    <div class="flex justify-center items-center mt-4">
        <button wire:click="adduser" class="btn btn-primary btn-sm gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-person-add" viewBox="0 0 16 16">
                <path
                    d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                <path
                    d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z" />
            </svg>
            상대추가
        </button>
    </div>

    @if ($popupForm)
        <x-wire-dialog-modal wire:model="popupForm" :maxWidth="$popupWindowWidth">
            <x-slot name="title">
                {{ __('맴버관리') }}
            </x-slot>

            <x-slot name="content">

                <div class="mb-3">
                    <label class="form-label">관리자</label>
                    <input type="checkbox" class="form-check-input"
                        wire:model.defer="forms.is_owner"
                        {{ isset($forms['is_owner']) && $forms['is_owner'] ? 'checked' : '' }}>
                </div>

                <div class="mb-3">
                    <label class="form-label">이메일</label>
                    <input type="text" class="form-control" wire:model.defer="forms.email">
                </div>

                <div class="mb-3">
                    <label class="form-label">언어</label>
                    <select class="form-select" wire:model.defer="forms.lang">
                        <option value="">언어선택</option>

                        @foreach (DB::table('site_chat_lang')->get() as $lang)
                            <option value="{{ $lang->lang }}">{{ $lang->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">색상</label>
                    <select class="form-select" wire:model.defer="forms.color">
                        <option value="">색상선택</option>

                        <option value="primary">파란색</option>
                        <option value="success">초록색</option>
                        <option value="danger">빨간색</option>
                        <option value="warning">노란색</option>
                        <option value="info">하늘색</option>
                        <option value="dark">검정색</option>
                    </select>
                </div>
            </x-slot>

            <x-slot name="footer">
                @if (isset($forms['id']))
                    <div class="d-flex justify-content-between">
                        <div>
                            <button wire:click="remove({{ $forms['id'] }})" class="btn btn-danger">
                                삭제
                            </button>
                        </div>
                        <div>
                            <button wire:click="$set('popupForm', false)" class="btn btn-secondary">
                                닫기
                            </button>
                            <button wire:click="update" class="btn btn-info">
                                수정
                            </button>
                        </div>
                    </div>
                @else
                    <div class="d-flex justify-content-between">
                        <div>
                            <button wire:click="$set('popupForm', false)" class="btn btn-secondary">
                                닫기
                            </button>
                        </div>
                        <div>
                            <button wire:click="store" class="btn btn-info">
                                저장
                            </button>
                        </div>
                    </div>
                @endif
            </x-slot>
        </x-wire-dialog-modal>
        {{-- <x-wire-dialog-modal wire:model="popupForm" :maxWidth="$popupWindowWidth">
            <x-slot name="title">
                {{ __('맴버관리') }}
            </x-slot>

            <x-slot name="content">

                @foreach ($rows as $row)
                    <div class="border-b p-2 mb-2 flex justify-between items-center">
                        <div class="flex items-center">
                            @if ($row->user_id)
                                <img src="/home/avatas/{{ $row->user_id }}"
                                    alt="User Avatar"
                                    class="w-8 h-8 rounded-full">
                            @else
                                <span class="w-8 h-8 bg-gray-300 rounded-full" style="display:inline-block"></span>
                            @endif

                            <span class="ml-2">
                                {{ $row->email }} ({{ $row->lang }})
                            </span>
                        </div>

                        <div>
                            <button wire:click="remove({{ $row->id }})" class="btn btn-danger btn-sm">
                                내보내기
                            </button>
                        </div>
                    </div>
                @endforeach



            </x-slot>

            <x-slot name="footer">
                <div class="d-flex justify-content-between">
                    <div>
                        <button wire:click="$set('popupForm', false)" class="btn btn-secondary">
                            닫기
                        </button>
                    </div>
                    <div>
                        <input type="text" class="form-control" wire:model="forms.lang">
                        <div class="input-group">
                            <input type="text" class="form-control" wire:model="forms.email">
                            <button wire:click="store" class="btn btn-primary">
                                추가
                            </button>
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-wire-dialog-modal> --}}
    @endif
</div>
