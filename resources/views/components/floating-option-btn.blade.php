<div class="except fab-container d-flex align-items-end flex-column">
    <div class="except fab shadow fab-content">
        <i class="except icons fa fa-ellipsis-v text-white" title="Actions"></i>
    </div>
    @php
        // $potQuery = DB::table('tbl_saved_estimates')
        //     ->where('pot_id', request()->get('pot_id'))
        //     ->where('emp_code', session('emp_code'))
        //     ->first();
    @endphp
    @if (session('edit_id'))
        <div class="except sub-button shadow btn btn-outline-success action" id="Save">
            <i class="except icons fa fa-save"></i>
        </div>
    @else
        <div class="except sub-button shadow btn btn-outline-info action" title="Update" id="Update">
            <i class="except icons fa fa-sync" title="Update"></i>
        </div>
    @endif
</div>
