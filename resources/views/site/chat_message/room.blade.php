<div>
    <h5>
        <a href="/home/chat" style="text-decoration:none; color:inherit;">채팅 목록</a>
    </h5>
    <ul class="list-group">
        @foreach($rooms as $room)
            <li class="list-group-item {{ ($room->code == $code) ? 'active' : '' }}">
                <div class="d-flex align-items-center gap-2">
                    @if($room->image)
                        <img src="{{ $room->image }}" class="rounded-circle" width="32" height="32">
                    @else
                        <div class="rounded-circle bg-gray-200" style="width:32px; height:32px;"></div>
                    @endif
                    <a href="/home/chat/message/{{ $room->code }}"
                        style="text-decoration:none; color:inherit; cursor:pointer;">
                        {{ $room->title }}
                    </a>
                </div>
            </li>
        @endforeach
    </ul>
</div>
