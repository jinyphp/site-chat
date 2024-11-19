<div>
    <div class="flex justify-center items-center">
        <button onclick="copyCode('{{ $chat['invite'] }}')" class="btn btn-secondary btn-sm gap-2 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16">
                <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"/>
                <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"/>
            </svg>
            초대코드 복사
        </button>

        <script>
        function copyCode(code) {
            navigator.clipboard.writeText('/home/chat/invite/'+code);
            alert('초대 코드가 복사되었습니다.');
        }
        </script>
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

        <button wire:click="exit" class="btn btn-danger btn-sm gap-2 ml-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
            </svg>
            나가기
        </button>
    </div>

    @if ($popupForm)
        <x-wire-dialog-modal wire:model="popupForm" :maxWidth="$popupWindowWidth">
            <x-slot name="title">
                {{ __('맴버관리') }}
            </x-slot>

            <x-slot name="content">

                @foreach ($rows as $row)
                    <div class="border-b p-2 mb-2 flex justify-between items-center">
                        <div class="flex items-center">
                            @if($row->user_id)
                                <img src="/home/avatas/{{ $row->user_id }}"
                                    alt="User Avatar"
                                    class="w-8 h-8 rounded-full">
                            @else
                                <span class="w-8 h-8 bg-gray-300 rounded-full" style="display:inline-block"></span>
                            @endif

                            <span class="ml-2">
                                {{ $row->email }}
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
                        <div class="input-group">
                            <input type="text" class="form-control" wire:model="forms.email">
                            <button wire:click="store" class="btn btn-primary">
                                추가
                            </button>
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-wire-dialog-modal>
    @endif
</div>
