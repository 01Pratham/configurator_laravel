<div class="form-group col-md-3">
    <div class="form-group px-2 light Product-group" id="{{ $prod . "_{$id}" }}">
        <span class="fa fa-remove text-danger"
            style="position: absolute; margin: 1px 1px 1px -7px; cursor: pointer; z-index : 1; margin-top:-2px;"
            onclick="$(this).parent().parent().remove()"> <strong><b style="font-size: x-large; font-family: system-ui">
                    &times;</b></strong></span>
        <select name="{{ "{$name}[{$category}][products][{$prod}_select]" }}" id="{{ "{$prod}_select_{$id}" }}"
            class="border-0 small Product-Select col-md-12" oninput ="updateUnit($(this))">
            @php
                $query = DB::table('tbl_product_list')->where('sec_category', $prod)->where('is_active', 1)->get();
                $input_box = true;
                $placeHolder = 'Quantity';
                $select_box = true;

            @endphp
            @foreach ($query as $row)
                @php
                    try {
                        $tbl_ui = DB::table('tbl_ui_options')
                            ->where('sec_category_name', $row->sec_category)
                            ->first();
                        if ($tbl_ui) {
                            $input_box = $tbl_ui->input_num == false ? false : true;
                            $placeHolder = !empty($tbl_ui->input_placeholder) ? $tbl_ui->input_placeholder : 'Quantity';
                            $select_box = $tbl_ui->select_box == false ? false : true;
                        }
                    } catch (Exception $e) {
                    }

                @endphp
                @if (!$select_box)
                    <option value="{{ $row->default_int }}">{{ $row->default_name }}</option>
                    @php
                        $prodId[0] = $row->id;
                        break;
                    @endphp
                @else
                    <option value="{{ $row->prod_int }}"
                        {{ $Data->value("{$name}.{$category}.{$prod}_select") == $row->prod_int ? 'selected' : '' }}>
                        {{ $row->product }}
                    </option>
                    @php
                        if ($Data->value("{$name}.{$category}.{$prod}_select") == $row->prod_int) {
                            $prodId[0] = $row->id;
                            continue;
                        }
                        $prodId[] = $row->id;
                    @endphp
                @endif
            @endforeach
        </select>
        <div class="input-group">

            <input type='number' aria-describedby="{{ "{$prod}unit_{$id}" }}" step="0.01"
                class='form-control small col-md-8' id='{{ "{$prod}_qty_{$id}" }}'
                {{ !$input_box ? 'readonly' : 'required' }} min=0 placeholder='{{ $placeHolder }}'
                name='{{ "{$name}[{$category}][products][{$prod}_qty]" }}' value="{{ $quantity ?? 0 }}">
            <span class="input-group-text text-center unit form-control col-4 bg-light p-1"
                id="{{ "{$prod}unit_{$id}" }}">
                @php
                    $productUnits = \App\Models\UnitMap::getProductUnit($prodId[0] ?? '');
                @endphp
                @if (count($productUnits) > 1)
                    <select name="{{ "{$name}[{$category}][products][{$prod}_unit]" }}"
                        id="{{ "{$prod}_unit_{$id}" }}" style="width : 100%; background: transparent;"
                        class="form-control border-0 ">
                        @foreach ($productUnits as $arr)
                            <option value="{{ $arr['id'] }}"
                                {{ $Data->value("{$name}.{$category}.{$prod}_unit") == $arr['id'] ? 'selected' : '' }}>
                                {{ $arr['unit_name'] }}
                            </option>
                        @endforeach
                    </select>
                @else
                    {{ $productUnits[0]['unit_name'] }}
                @endif
            </span>
        </div>
    </div>

    <script>
        function updateUnit($this) {
            let prod = $this.val();
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                url: "/Ajax/GetProductUnit",
                type: "post",
                data: {
                    prod: prod,
                },
                success: function(response) {
                    $this.parent().find(".unit").html(response);
                }
            })
        }
    </script>
</div>
