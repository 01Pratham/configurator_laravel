<section class="est_div align-center Main mt-2" id="est_div_{{ $array['id'] }}">
    <div class="contain-btn est-head-div btn-link shadow-sm light " id="contain-btn_{{ $array['id'] }}">
        @if ($array['type'] == 'ajax')
            <input
                onclick="$('#count_of_est').val(parseInt($('#count_of_est').val()) - 1);$(this).parent().parent().remove(); "
                class="add-estmt btn btn-link except text-primary" type="button" role="button" title="Remove Estimate"
                id="rem-estmt_{{ $array['id'] }}" style="z-index: 1;" value="&times;">
        @else
            <input class="add-estmt btn btn-link except text-primary" type="button" role="button" title="Add Estimate"
                id="add-estmt" style="z-index: 1;" value="&plus;" onclick="add_estmt({{ $prod_list }});">
        @endif
        <input type="checkbox" id="checkHead_{{ $array['id'] }}" class="head-btn d-none"
            onclick="
            $('#estmt_collapse_{{ $array['id'] }}').hasClass('hiddenDiv') ? $('#estmt_collapse_{{ $array['id'] }}').removeClass('hiddenDiv'):$('#estmt_collapse_{{ $array['id'] }}').addClass('hiddenDiv');
        ">
        <label class="text-left text-primary pt-3" for="checkHead_{{ $array['id'] }}"
            id="estmtHead_{{ $array['id'] }}" style="z-index: 1;">
            <h6 class="OnInput">
                @if ($Data->value("{$array['name']}.estmtname") != '' && $Data->value("{$array['name']}.estmtname") != 0)
                    {{ $Data->value("{$array['name']}.estmtname") }}
                @else
                    Your Estimate
                @endif
            </h6>
        </label>
        <span class="float-right pt-2">
            <select name="{{ $array['name'] }}[region]" id="region_{{ $array['id'] }}" class="border-0 text-primary">
                @foreach ($regions as $region)
                    @if ($region['id'] != 0)
                        <option value = '{{ $region['id'] }}'>{{ $region['region_name'] }} </option>
                    @endif
                @endforeach
            </select>
            <button class="clone-estmt btn btn-link except text-primary" type="button" role="button"
                title="Clone Estimate" id="clone-estmt_{{ $array['id'] }}" data-id="{{ $array['id'] }}"
                data-name="{{ $array['name'] }}" style="z-index: 1;"
                onclick="event.preventDefault(); cloneEst($(this));"><i class="fa fa-copy except"></i></button>
        </span>
    </div>
    <div class="my-1 except" id="estmt_collapse_{{ $array['id'] }}">
        <div class="tab card card-body">
            <div class="form-row">
                <div class="form-group col-md-9">
                    <input type="text" class="form-control EstmtName" id="estmtname_{{ $array['id'] }}"
                        data-id="{{ $array['id'] }}" data-name="{{ $array['name'] }}" placeholder="Your Estimate"
                        name="{{ $array['name'] }}[estmtname]" required
                        value="{{ $Data->value("{$array['name']}.estmtname") != 0 ? $Data->value("{$array['name']}.estmtname") : '' }}"
                        onload="addLineItemsToDropdownMenu({{ $prod_list }})"
                        onchange="addLineItemsToDropdownMenu({{ $prod_list }})"
                        oninput="const lbl =  $(this).parent().parent().parent().parent().siblings('.est-head-div').find('.OnInput');
                             lbl.html(escapeHtml($(this).val() == ''?'Your Estimate':$(this).val()))">
                </div>
                <div class="col-md-3 input-group ">
                    <input type="number" min=0 class="form-control small col-8 text-sm-left"
                        id="period_{{ $array['id'] }}" placeholder="Contract Period" min=1
                        name="{{ $array['name'] }}[period]" required
                        value="{{ $Data->value("{$array['name']}.period") != 0 ? $Data->value("{$array['name']}.period") : '' }}"
                        aria-describedby="PeriodUnit_{{ $array['id'] }}" style="font-size:15">
                    <span class="input-group-text form-control col-4 bg-light"
                        id="PeriodUnit_{{ $array['id'] }}">Months</span>
                </div>
            </div>
            <div id="virtual_machine_{{ $array['name'] }}">
                <input type="hidden" name="{{ $array['name'] }}[count_of_virtual_machine]"
                    id="count_of_virtual_machine_{{ $array['name'] }}"
                    value="{{ intval($Data->value("{$array['name']}.count_of_virtual_machine")) }}">
                @if ($Data->value("{$array['name']}.count_of_virtual_machine") > 0)
                    @for ($i = 1; $i <= $Data->value("{$array['name']}.count_of_virtual_machine"); $i++)
                        @include('layouts.virtual-machine', [
                            'name' => $array['name'],
                            'id' => $array['name'] . $i,
                            'list_id' => $prod_list,
                            'Data' => $Data,
                        ])
                    @endfor
                @endif
            </div>

            <div id="block-storage_{{ $array['name'] }}">
                <input type="hidden" name="{{ $array['name'] }}[count_of_block_storage]"
                    id="count_of_block_storage_{{ $array['name'] }}"
                    value="{{ intval($Data->value("{$array['name']}.count_of_block_storage")) }}">
                @if ($Data->value("{$array['name']}.count_of_block_storage") > 0)
                    @for ($i = 1; $i <= $Data->value("{$array['name']}.count_of_block_storage"); $i++)
                        @include('layouts.block-storage', [
                            'name' => $array['name'],
                            'id' => $array['name'] . $i,
                            'list_id' => $prod_list,
                            'Data' => $Data,
                        ])
                    @endfor
                @endif
            </div>
            @if ($Data->value("{$array['name']}", []) != 0)

                @if (!empty($Data->value("{$array['name']}", [])) || gettype($Data->value("{$array['name']}", [])) != 'string')
                    @foreach ($Data->value("{$array['name']}", []) as $category => $arr)
                        @php
                            if (preg_match('/vm|strg_[1-9]/', $category) || !is_array($arr)) {
                                continue;
                            }

                            $id = $array['id'];
                            $name = $array['name'];
                        @endphp
                        @if (!empty($arr))
                            @include('components.product-group', [
                                'id' => $array['id'],
                                'name' => $array['name'],
                                'category' => $category,
                                'arr' => $arr,
                                'Data' => $Data,
                            ])
                        @endif
                    @endforeach
                @endif
            @endif
        </div>
    </div>
</section>

<script>
    get_default();

    $('#add-vm_{{ $array['name'] }}').click(function() {
        name = $(this).prop('id')
        name = name.replace('add-vm_', '')
        add_vm(name, {{ $array['id'] }}, {{ $prod_list }});
    })
    $(document).ready(function() {
        $('.Checked').each(function() {
            $(this).attr("checked", "true")
            $(this).parent().find('input[type="number"]').attr('required', 'true')
            let id = $(this).parent().find('select').prop('id');
            if ($("#" + id + " option").length > 1) {
                if ($("#" + id).val() === '') {
                    $("#" + id).attr('required', 'true');
                }
            } else {}
        })
        $('.replink').addClass('d-none');
    })
</script>
