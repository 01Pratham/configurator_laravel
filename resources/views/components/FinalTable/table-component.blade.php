<table class='final-tbl table except' id='final-tbl{{ $KEY }}'>
    <thead class="except">
        <tr hidden class = 'extraLine'></tr>
        <tr hidden class = 'extraLine'></tr>
        <tr hidden class = 'extraLine'></tr>
        <tr hidden class = 'extraLine'></tr>
        <tr hidden class = 'extraLine'></tr>
        <tr hidden class = 'extraLine'></tr>
        <tr hidden class = 'extraLine'></tr>
    </thead>
    <tbody class="except">
        <tr class = 'noExl'>
            @php
                $colspan = Route::is('Discounting') ? 10 : 8;
            @endphp
            <th class='Head colspan except' colspan='{{ $colspan }}' style='font-size: 30px;'>
                <div class='row except d-flex justify-content-between'>
                    <div class='except'></div>
                    <div class='except'>
                        {{ $VAL['estmtname'] }}
                    </div>
                    <div class='col-2 except input-group'>
                        <input type="hidden" id='DiscountPercetage_{{ $KEY }}'
                            value="{{ $VAL['percentage'] }}">
                        <input type='number' min=0 max=100 id="demo-percentage-{{ $KEY }}"
                            class='form-control col-md-10 ' @if (!@Route::is('Discounting')) disabled @endif
                            value="{{ $VAL['percentage'] }}" aria-describedby='perce_{{ $KEY }}'>
                        <button class='input-group-text form-control bg-light col-2 p-0 d-flex justify-content-center'
                            id='perce_{{ $KEY }}' style='cursor : pointer'
                            onclick="let p = !isNaN($('#demo-percentage-{{ $KEY }}').val())?$('#demo-percentage-{{ $KEY }}').val():$('#DiscountPercetage_{{ $KEY }}').val();
                            $('#DiscountPercetage_{{ $KEY }}').val(parseFloat(p))">
                            %
                        </button>
                    </div>
                </div>
            </th>
        </tr>
        @if (is_array($VAL))
            @foreach ($VAL as $Key => $Val)
                @if (is_array($Val))
                    @php
                        if (preg_match('/managed/', $Key)) {
                            $Class = "Managed_{$KEY}";
                        } else {
                            $Class = "Infrastructure_{$KEY}";
                        }

                        $NO = isset($NO) ? ++$NO : 1;
                    @endphp
                    @include('components.FinalTable.table-head-component', [
                        'Service' => $Key,
                        'NO' => $NO,
                    ])
                    @foreach ($Val as $key => $val)
                        @if (isset($val['mrc']))
                            @include('components.FinalTable.table-row-component', [
                                'arr' => $val,
                                'keys' => [
                                    'Class' => $Class,
                                    'KEY' => $KEY,
                                    '_K' => $Key,
                                    '_k' => $key,
                                ],
                            ])
                        @else
                            @if (is_array($val))
                                @foreach ($val as $_K => $_V)
                                    @include('components.FinalTable.table-row-component', [
                                        'arr' => $_V,
                                        'keys' => [
                                            'Class' => $Class,
                                            'KEY' => $KEY,
                                            '_K' => $Key,
                                            '_k' => $key,
                                            '__k' => $_K,
                                        ],
                                        'rowspan' => $_K == 'vcore' ? 'rowspan = 3' : 'hidden',
                                    ])
                                @endforeach
                            @endif
                        @endif
                    @endforeach
                @endif
            @endforeach
        @endif

        @include('components.FinalTable.table-total-component', $Total[$KEY])
    </tbody>

</table>
@if (@Route::is('Discounting'))
    <script>
        $("#perce_{{ $KEY }}").on("click", function() {
            var $obj = {
                action: "Discount",
                discountVal: $("#DiscountPercetage_{{ $KEY }}").val() / 100,
                Total: "{{ $Total[$KEY]['MONTHLY_TOTAL'] }}",
                data: `{{ base64_encode(json_encode($Products)) }}`,
            };
            DiscountingAjax($obj, {{ $KEY }});
        })
    </script>
@endif
