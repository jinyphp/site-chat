<div wire:poll.5s>
    <div class="text-center">
        <p>들어가기 위해서는 접속 암호가 필요합니다.</p>

        <div class="mt-4 col-md-6 mx-auto">
            <div class="input-group">
                <input type="password"
                    wire:model.defer="password"
                    class="form-control"
                    placeholder="비밀번호를 입력해 주세요">
                <button wire:click="checkPassword"
                    class="btn btn-primary">
                    확인
                </button>
            </div>
        </div>
    </div>
</div>
