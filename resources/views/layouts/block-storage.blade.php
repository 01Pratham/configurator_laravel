<div>
    <div class="contain-btn btn-link border-bottom " id='block_strg_head_<?= $id ?>'>
        <a class="btn btn-link text-left" id="" data-toggle="collapse" href="#block_strg_collapse_<?= $id ?>"
            role="button" aria-expanded="true" aria-controls="block_strg_collapse_<?= $id ?>">
            <i class="fa fa-database"></i>
            <h6 class="d-inline-block ml-1">Additional Block Storage : </h6>
            <h6 class="d-inline-block ml-1 OnInput"></h6>
        </a>
        <input type="button" value=" Remove " class="add-estmt btn btn-link float-right except"
            id="block_strg_rem_<?= $id ?>" data-toggle="button" aria-pressed="flase" autocomplete="on"
            onclick="
        $(this).parent().parent().remove()
        $(`#count_of_block_storage_<?= $name ?>`).val(parseInt($(`#count_of_block_storage_<?= $name ?>`).val())-1)
        ">
        <?php
        if ($type != "Ajax") {
        ?>
        <span class="btn btn-link float-right except"><strong>|</strong></span>
        <input type="button" value=" Add New " onclick="addBlockStorage(<?= $name . ' , ' . $id ?>)"
            class="add-estmt btn btn-link float-right except" id="block_strg_rem_<?= $id ?>" data-toggle="button"
            aria-pressed="flase" autocomplete="on">
        <?php } ?>
    </div>

    <div class="form-group col-md-6 px-2 light Product-group" id="block_strg_collapse_<?= $id ?>">
        <span class="fa fa-remove text-danger"
            style="position: absolute; margin: 4px 0px 0px -7px; cursor: pointer; z-index : 1;"
            onclick="$(this).parent().remove()"></span>
        <select id="block_strg_select_<?= $id ?>" class="border-0 small Product-Select col-md-12">
            <option value=""><?= 'Block Storage' ?></option>
        </select>
        <div class="row">
            <div class="input-group col-md-6">
                <input type='number' aria-describedby="" step="0.01" class='form-control small col-md-8'
                    id='block_strg_capacity_<?= $id ?>' min=0 placeholder='Capacity'
                    name='<?= "{$name}[strg_{$id}][strg_capacity]" ?>'
                    value="{{ $Data->value("{$name}.strg_{$id}.strg_capacity") }}">
                <span class="input-group-text text-center unit form-control col-4 bg-light p-1"
                    id="block_strg_unit_span_<?= $id ?>">
                    <select name="<?= "{$name}[strg_{$id}][strg_unit]" ?>" id="block_strg_unit_<?= $id ?>"
                        style="width : 100%; background: transparent;" class="form-control border-0 ">
                        <option {{ $Data->value("{$name}.strg_{$id}.strg_unit") == 'GB' ? 'selected' : '' }}
                            value="GB">GB</option>
                        <option {{ $Data->value("{$name}.strg_{$id}.strg_unit") == 'TB' ? 'selected' : '' }}
                            value="TB">TB</option>
                    </select>
                </span>
            </div>
            <div class="input-group col-md-6">
                <input type='number' aria-describedby="" step="0.01" class='form-control small col-md-8'
                    id='block_strg_iops_<?= $id ?>' min=0 placeholder='Capacity'
                    name='<?= "{$name}[strg_{$id}][strg_iops]" ?>'
                    value="{{ $Data->value("{$name}.strg_{$id}.strg_iops") }}">
                <span class="input-group-text text-center unit form-control col-4 bg-light p-1"
                    id="block_strg_iops_span_<?= $id ?>">IOPS</span>
            </div>
        </div>
    </div>
</div>
