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
