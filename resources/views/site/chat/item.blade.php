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
                    <div class="d-flex justify-content-between">
                        <div class="fs-xs text-body-secondary me-3">
                            {{ $room->created_at }}
                        </div>
                        <div class="badge bg-secondary" >
                            {{ $room->user_cnt }}명
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <h3 class="h5 mb-2">
                            <a class=""
                            href="/home/chat/message/{{ $room->code }}">{{ $room->title }}</a>
                        </h3>

                        @if($room->last_message_at > $room->last_checked_at)
                        <div class="text-xs">
                            {{-- {{ $room->last_message_at }} /
                            {{ $room->last_checked_at }} --}}
                            <span class="badge bg-danger">New Message</span>
                        </div>
                        @endif

                    </div>

                    <p class="fs-sm pt-2 mt-1 mb-0">
                        {{ $room->description }}
                    </p>



                    <hr>

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
