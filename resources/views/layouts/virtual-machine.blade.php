@php
    $CONST_KEY = $name - 1 . '.groups.' . substr($id, 1) - 1;
@endphp
<div>
    <input type="hidden" name="{{ "{$name}[vm_{$id}][group_id]" }}" value="{{ __($Data->value($CONST_KEY . '.id')) }}">
    <div class="contain-btn btn-link border-bottom VMhead" id='vmHead_{{ $id }}'>
        <a class="btn btn-link text-left" id="vmHead_{{ $id }}" data-toggle="collapse"
            href="#vm_collapse_{{ $id }}" role="button" aria-expanded="true"
            aria-controls="vm_collapse_{{ $id }}">
            <i class="fa fa-desktop"></i>
            <h6 class="d-inline-block ml-1">Virtual Machine :
                {{ __($Data->value($CONST_KEY . '.group_name')) }}
            </h6>
            <h6 class="d-inline-block ml-1 OnInput"></h6>
        </a>
        <input type="button" value=" Remove " class="add-estmt btn btn-link float-right except"
            id="rem-vm_{{ $id }}" data-toggle="button" aria-pressed="false" autocomplete="on"
            onclick="$(this).parent().parent().remove();
            $(`#count_of_virtual_machine_{{ $name }}`).val(parseInt($(`#count_of_virtual_machine_` + {{ $name }}).val()) - 1);">
        <button class=" clone-vm btn btn-link float-right except text-primary" type="button" role="button"
            title="Clone VM" id="clone-vm_{{ $id }}" data-id="{{ $id }}"
            data-name="{{ $name }}" style="z-index: 1;" onclick="event.preventDefault(); cloneVM($(this))">
            <i class="fa fa-copy except"></i>
        </button>

    </div>
    <div class="collapse show py-1" id="vm_collapse_{{ $id }}">
        <div class="row">
            <div class="col-9">
                <h6><small>VM Name :</small></h6>
                <input type="text" class="form-control" id="vmname_{{ $id }}"
                    placeholder="Virtual Machine" name="{{ "{$name}[vm_{$id}][products][vmname]" }}"
                    value="{{ __($Data->value($CONST_KEY . '.group_name')) }}"
                    oninput="
                    $inputSpan = $(this).parent().parent().parent().siblings('.VMhead').find('.OnInput')
                    $inputSpan.html(escapeHtml($(this).val()))">
            </div>
            <div class="col-3">
                <h6><small>Quantity :</small></h6>
                <input type="number" class="form-control small" id="vmqty_{{ $id }}" min=0
                    placeholder="Quantity" value="{{ __($Data->value($CONST_KEY . '.group_quantity')) }}"
                    name="{{ "{$name}[vm_{$id}][products][vmqty]" }}">
            </div>
        </div>
        <div class="form-row mt-2">
            <div class="form-group col-md-9 px-2">
                <h6><small>Instance :</small></h6>
                <div class="row flexComp">
                    <div class="col-4 input-group">
                        <span class="input-group-text form-control col-5 bg-transparent border-right-0 text-sm"
                            id="vcpu_lbl_{{ $id }}">vCPU </span>
                        <span
                            class="input-group-text form-control col-1 bg-transparent border-right-0 border-left-0 text-sm"
                            id="vcpu_lbl_{{ $id }}"> : </span>
                        <input type="number" class="form-control small col-6 text-sm-left border-left-0"
                            id="vcpu_{{ $id }}" min=1 placeholder="Quantity"
                            value="{{ $Data->value("$CONST_KEY.products.vcpu.quantity") }}"
                            name="{{ "{$name}[vm_{$id}][products][vcpu]" }}">
                    </div>
                    <div class="col-4 input-group">
                        <span class="input-group-text form-control col-5 bg-transparent border-right-0 text-sm"
                            id="ram_{{ $id }}">vRAM </span>
                        <span
                            class="input-group-text form-control col-1 bg-transparent border-right-0 border-left-0 text-sm"
                            id="ram_{{ $id }}"> : </span>
                        <input type="number" class="form-control small col-6 text-sm-left border-left-0"
                            id="ram_{{ $id }}" min=1 placeholder="Quantity"
                            value="{{ $Data->value("$CONST_KEY.products.ram.quantity") }}"
                            name="{{ "{$name}[vm_{$id}][products][ram]" }}">
                    </div>
                    <div class="col-4 input-group">
                        <span class="input-group-text form-control col-5 p-0 bg-transparent border-0 "
                            id="inst_disk_{{ $id }}">
                            <select name="{{ "{$name}[vm_{$id}][products][vmDiskIOPS]" }}"
                                id="disk_{{ $id }}" class="form-control p-0 text-sm  border-right-0">
                                @php
                                    $products = \App\Models\ProductList::select(
                                        'tbl_product_list.product',
                                        'tbl_product_list.prod_int',
                                    )
                                        ->join(
                                            'tbl_rate_card_prices',
                                            'tbl_product_list.id',
                                            '=',
                                            'tbl_rate_card_prices.prod_id',
                                        )
                                        ->where('tbl_product_list.sec_category', 'block_storage')
                                        ->where('tbl_rate_card_prices.rate_card_id', $list_id)
                                        ->distinct()
                                        ->get();
                                @endphp

                                @foreach ($products as $product)
                                    @php
                                        $iops = preg_replace('/[A-Za-z]/', '', $product->product) . ' IOPS/GB';
                                    @endphp
                                    <option value="{{ $product->prod_int }}"
                                        @if ($Data->value("$CONST_KEY.products.block_storage.prod_int") == $product->prod_int) selected @endif>
                                        {{ $iops }}
                                    </option>
                                @endforeach
                            </select>
                        </span>
                        <span
                            class="input-group-text form-control col-1 bg-transparent border-right-0 border-left-0 text-sm"
                            id="inst_disk_{{ $id }}"> : </span>
                        <input type="number" class="form-control small col-6 text-sm-left border-left-0"
                            id="inst_disk_{{ $id }}" min=1 placeholder="Quantity"
                            value="{{ $Data->value("$CONST_KEY.products.block_storage.quantity") }}"
                            name="{{ "{$name}[vm_{$id}][products][inst_disk]" }}">
                    </div>
                </div>
            </div>
            <div class="form-group col-md-3 px-2">
                <h6><small>VM State :</small></h6>
                <select name="{{ "{$name}[vm_{$id}][products][state]" }}" id="state_{{ $id }}"
                    class="form-control">
                    <option
                        {{ $Data->value("{$name}.vm_{$id}.state") == 'Standalone' ? __('selected') : '' }}value="Standalone">
                        Standalone</option>
                    <option
                        {{ $Data->value("{$name}.vm_{$id}.state") == 'Active' ? __('selected') : '' }}value="Active"
                        class="single">Active</option>
                    <option
                        {{ $Data->value("{$name}.vm_{$id}.state") == 'Passive' ? __('selected') : '' }}value="Passive"
                        class="single">Passive</option>
                    <option
                        {{ $Data->value("{$name}.vm_{$id}.state") == 'Active-Active' ? __('selected') : '' }}value="Active-Active"
                        class="multiple">Active-Active</option>
                    <option
                        {{ $Data->value("{$name}.vm_{$id}.state") == 'Active-Passive' ? __('selected') : '' }}value="Active-Passive"
                        class="multiple">Active-Passive</option>
                </select>
                <script>
                    $('#vmqty_{{ $id }}').on("input", function() {
                        if ($(this).val() < 2) {
                            $('#state_{{ $id }} .multiple').attr("hidden", "true");
                            $('#state_{{ $id }} .single').removeAttr("hidden");
                        } else {
                            $('#state_{{ $id }} .single').attr("hidden", "true");
                            $('#state_{{ $id }} .multiple').removeAttr('hidden');
                        }
                    })
                    if ($('#vmqty_{{ $id }}').val() < 2) {
                        $('#state_{{ $id }} .multiple').attr("hidden", "true");
                        $('#state_{{ $id }} .single').removeAttr("hidden");
                    } else {
                        $('#state_{{ $id }} .single').attr("hidden", "true");
                        $('#state_{{ $id }} .multiple').removeAttr('hidden');
                    }
                </script>
            </div>
            <div class="form-group col-md-3 px-2">
                <h6><small>Operating System :</small></h6>
                <select name="{{ "{$name}[vm_{$id}][products][os]" }}" id="os_{{ $id }}"
                    class="form-control" required>
                    <option value="" hidden>Select OS</option>
                    @php
                        $query = \App\Models\ProductList::select(
                            'tbl_product_list.product',
                            'tbl_product_list.prod_int',
                        )
                            ->join('tbl_rate_card_prices', 'tbl_product_list.id', '=', 'tbl_rate_card_prices.prod_id')
                            ->where('tbl_product_list.sec_category', 'os')
                            ->where('tbl_rate_card_prices.rate_card_id', $list_id)
                            ->get();
                    @endphp
                    @foreach ($query as $row)
                        <option value="{{ $row->prod_int }}"
                            {{ $Data->value("$CONST_KEY.products.os.prod_int") == $row->prod_int ? 'selected' : '' }}>
                            {{ $row->product }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-3 px-2">
                <h6><small>Database :</small></h6>
                <select name="{{ "{$name}[vm_{$id}][products][database]" }}" id="db_{{ $id }}"
                    class="form-control" required>
                    <option value="" hidden>Select DB</option>
                    <option value="NA"
                        {{ $Data->value("$CONST_KEY.products.db.prod_int") == 'NA' ? 'selected' : '' }}>
                        NA</option>
                    @php
                        $query = \App\Models\ProductList::select(
                            'tbl_product_list.product',
                            'tbl_product_list.prod_int',
                        )
                            ->join('tbl_rate_card_prices', 'tbl_product_list.id', '=', 'tbl_rate_card_prices.prod_id')
                            ->where('tbl_product_list.sec_category', 'db')
                            ->where('tbl_rate_card_prices.rate_card_id', $list_id)
                            ->get();
                    @endphp
                    @foreach ($query as $row)
                        <option value="{{ $row->prod_int }}"
                            {{ $Data->value("$CONST_KEY.products.db.prod_int") == $row->prod_int ? 'selected' : '' }}>
                            {{ $row->product }}
                        </option>
                    @endforeach
                    <option value="Other" contenteditable="true"
                        {{ $Data->value("$CONST_KEY.products.db.prod_int") == 'Other' ? 'selected' : '' }}>Other
                    </option>
                </select>
            </div>
            <div class="form-group col-md-3 px-2 ">
                <select name="{{ "{$name}[vm_{$id}][products][ip_public_type]" }}" id="ip_public{{ $id }}"
                    class="border-0 small h6" style="width: 100%;">
                    @php
                        $query = \App\Models\ProductList::select(
                            'tbl_product_list.product',
                            'tbl_product_list.prod_int',
                        )
                            ->join('tbl_rate_card_prices', 'tbl_product_list.id', '=', 'tbl_rate_card_prices.prod_id')
                            ->where('tbl_product_list.sec_category', 'ip')
                            ->where('tbl_rate_card_prices.rate_card_id', $list_id)
                            ->get();
                    @endphp
                    @foreach ($query as $row)
                        <option value="{{ $row->prod_int }}"
                            {{ $Data->value("$CONST_KEY.products.ip.prod_int") == $row->prod_int ? 'selected' : '' }}>
                            {{ $row->product }}
                        </option>
                    @endforeach
                </select>
                <input type="number" class="form-control small" id="ip_public_{{ $id }}" min=0
                    placeholder="Quantity" value="{{ $Data->value("$CONST_KEY.products.ip.quantity") ?? 0 }}"
                    name="{{ "{$name}[vm_{$id}][products][ip_public]" }}">
            </div>
            <div class="form-group col-md-3 px-2">
                <h6><small>Anti-Virus : </small></h6>
                <select name="{{ "{$name}[vm_{$id}][products][virus_type]" }}" id="virus_type_{{ $id }}"
                    class="form-control">
                    <option value="">Select Antivirus</option>
                    @php
                        $query = \App\Models\ProductList::select(
                            'tbl_product_list.product',
                            'tbl_product_list.prod_int',
                        )
                            ->join('tbl_rate_card_prices', 'tbl_product_list.id', '=', 'tbl_rate_card_prices.prod_id')
                            ->where('tbl_product_list.sec_category', 'av')
                            ->where('tbl_rate_card_prices.rate_card_id', $list_id)
                            ->get();
                    @endphp
                    @foreach ($query as $row)
                        <option value="{{ $row->prod_int }}"
                            {{ $Data->value("$CONST_KEY.products.av.prod_int") == $row->prod_int ? 'selected' : '' }}>
                            {{ $row->product }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
