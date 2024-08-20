<div id="{{ $category }}_{{ $id }}">
    <div class="contain-btn btn-link border-bottom " id='{{ $category }}_head_{{ $id }}'>
        <a class="btn btn-link text-left" id="{{ $category }}_head_{{ $id }}" data-toggle="collapse"
            href="#{{ $category }}collapse_{{ $id }}" role="button" aria-expanded="true"
            aria-controls="{{ $category }}collapse_{{ $id }}">
            <i class="fa fa-box except"></i>
            <h6 class="d-inline-block ml-1">
                {{ ucwords(preg_replace('/_/', ' ', $category)) }} Services :
            </h6>
            <h6 class="d-inline-block ml-1 OnInput"></h6>
        </a>
        <input type="button" value=" Remove " class="add-estmt btn btn-link float-right except remove"
            id="rem-vm_{{ $id }}" data-toggle="button" aria-pressed="flase" autocomplete="on"
            onclick="$(this).parent().parent().remove()">
    </div>
    <div class="collapse show py-1" id="{{ $category }}collapse_{{ $id }}">
        <div class="row main-row">
            @if (Route::is('ProductAjax'))
                @include('components.product-elem', [
                    'id' => $id,
                    'prod' => $prod,
                    'name' => $name,
                    'category' => $category,
                    'Data' => $Data,
                    'request' => $request,
                ])
            @else
                @foreach ($arr as $prod => $val)
                    @if (preg_match('/qty/', $prod))
                        @include('components.product-elem', [
                            'id' => $id,
                            'prod' => preg_replace('/_qty/', '', $prod),
                            'name' => $name,
                            'category' => $category,
                            'Data' => $Data,
                        ])
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</div>
