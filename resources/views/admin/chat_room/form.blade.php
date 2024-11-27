<div>
    <x-navtab class="mb-3 nav-bordered">

        <!-- formTab -->
        <x-navtab-item class="show active" >

            <x-navtab-link class="rounded-0 active">
                <span class="d-none d-md-block">기본정보</span>
            </x-navtab-link>

            <x-form-hor>
                <x-form-label>언어</x-form-label>
                <x-form-item>
                    {{-- {!! xInputText()
                        ->setWire('model.defer',"forms.lang")
                        ->setWidth("standard")
                    !!} --}}
                    <select class="form-select" wire:model.defer="forms.lang">
                        <option value="ko">한국어(ko)</option>
                        <option value="en">영어(en)</option>
                        <option value="ja">일본어(ja)</option>
                        <option value="zh">중국어(zh)</option>
                        <option value="de">독일어(de)</option>
                        <option value="fr">프랑스어(fr)</option>
                        <option value="es">스페인어(es)</option>
                        <option value="pt">포르투갈어(pt)</option>
                    </select>
                </x-form-item>
            </x-form-hor>

            <x-form-hor>
                <x-form-label>이름</x-form-label>
                <x-form-item>
                    {!! xInputText()
                        ->setWire('model.defer',"forms.name")
                        ->setWidth("standard")
                    !!}
                </x-form-item>
            </x-form-hor>





        </x-navtab-item>



    </x-navtab>
</div>
