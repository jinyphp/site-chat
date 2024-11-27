<x-wire-table>
    <x-wire-thead>
        <th width='50'>Id</th>
        <th width='100'>code</th>
        <th >
            타이틀
        </th>
        <th width='200'>참여자</th>
        <th width='200'>등록일자</th>
    </x-wire-thead>
    <tbody>
        @if(!empty($rows))
            @foreach ($rows as $item)
            <x-wire-tbody-item :selected="$selected" :item="$item">
                {{-- 테이블 리스트 --}}
                <td width='50'>{{$item->id}}</td>
                <td width='100'>{{$item->code}}</td>
                <td >
                    {{-- {!! $popupEdit($item, $item->name) !!} --}}
                    <x-link-void wire:click="edit({{$item->id}})">
                        {{$item->title}}
                    </x-link-void>
                    <p>{{$item->description}}</p>
                </td>
                <td width='200'>
                    <a href="/admin/chat/room/{{$item->code}}">
                        {{$item->user_cnt}}
                    </a>

                </td>
                <td width='200'>{{$item->created_at}}</td>

            </x-wire-tbody-item>
            @endforeach
        @endif
    </tbody>
</x-wire-table>
