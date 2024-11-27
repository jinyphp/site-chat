<div>
    <x-navtab class="mb-3 nav-bordered">

        <!-- formTab -->
        <x-navtab-item class="show active" >

            <x-navtab-link class="rounded-0 active">
                <span class="d-none d-md-block">기본정보</span>
            </x-navtab-link>

            <x-form-hor>
                <x-form-label>코드</x-form-label>
                <x-form-item>
                    {!! xInputText()
                        ->setWire('model.defer',"forms.code")
                        ->setWidth("standard")
                    !!}
                </x-form-item>
            </x-form-hor>

            <x-form-hor>
                <x-form-label>타이틀</x-form-label>
                <x-form-item>
                    {!! xInputText()
                        ->setWire('model.defer',"forms.title")
                        ->setWidth("standard")
                    !!}
                </x-form-item>
            </x-form-hor>

            <x-form-hor>
                <x-form-label>패스워드</x-form-label>
                <x-form-item>
                    {!! xInputText()
                        ->setWire('model.defer',"forms.password")
                        ->setWidth("standard")
                    !!}
                </x-form-item>
            </x-form-hor>

            <x-form-hor>
                <x-form-label>이미지</x-form-label>
                <x-form-item>
                    <div wire:loading wire:target="forms.image"
                        class="inline-flex items-center ml-2">
                        <div class="spinner-border spinner-border-sm text-primary"
                            role="status">
                            <span class="visually-hidden">업로드 중...</span>
                        </div>
                        <span class="text-primary ms-2">파일 업로드 중...</span>
                    </div>
                    <input type="file" class="form-control" id="imageUpload"
                        wire:model.live="forms.image"
                        accept="image/*"
                        wire:loading.attr="disabled">
                    @if(isset($forms['image']))
                        <div class="">
                            {{ $forms['image'] }}
                        </div>
                    @endif

                </x-form-item>
            </x-form-hor>

            <x-form-hor>
                <x-form-label>설명</x-form-label>
                <x-form-item>
                    {!! xTextarea()
                        ->setWire('model.defer',"forms.description")
                    !!}
                </x-form-item>
            </x-form-hor>



        </x-navtab-item>



    </x-navtab>
</div>
