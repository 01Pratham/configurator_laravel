@extends('layouts.main-layout')

@section('main')
    @include('components.content-header', [
        'array' => ['Estimate' => route('Estimate')],
    ])
    <div class="content Main">
        @include('components.product-container', [
            'Categories' => $Categories,
            'Products' => $Products,
            'prod_list' => $prod_list,
        ])
    </div>

    <div class="container mt-2 Main">
        <form action="{{ route('FinalQuotation') }}" class="form1" id="form1" method="post">
            @csrf
            <div class="d-none">
                @foreach ($post_array as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach
            </div>
            <div class="mytabs my-2 accent-blue" id="myTab">
                <input type="hidden" name="count_of_est" id="count_of_est"
                    value="{{ empty($Data->value('count_of_est')) ? 1 : $Data->value('count_of_est') }}">

                @include('layouts.estmt-tab', [
                    'array' => ['name' => 1, 'id' => 1 . '1', 'type' => ''],
                    'prod_list' => $prod_list,
                    'regions' => $regions,
                    'Data' => $Data,
                ])

                @if ($Data->value('count_of_est') > 1)
                    @for ($i = 1; $i < $Data->value('count_of_est'); $i++)
                        @include('layouts.estmt-tab', [
                            'array' => ['name' => $i, 'id' => $i . '1', 'type' => 'ajax'],
                            'prod_list' => $prod_list,
                            'regions' => $regions,
                            'Data' => $Data,
                        ])
                    @endfor
                @endif
            </div>

            <div class="light py-2 rounded d-flex justify-content-center my-4">
                <button class="Next-Btn" name="proceed" formtarget="_blank">
                    Proceed
                    <i class="px-2 py-2  fa fa-angle-double-right"></i>
                </button>
            </div>

            <div class="except fab-container d-flex align-items-end flex-column">
                <div class="except fab shadow fab-content">
                    <i class="except icons fa fa-ellipsis-v text-white" title="Actions"></i>
                </div>
                @php
                    $potQuery = DB::table('tbl_saved_estimates')
                        ->where('pot_id', request()->get('pot_id'))
                        ->where('emp_code', session('emp_code'))
                        ->first();
                    echo session('edit_id');
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
        </form>
    </div>

    <script>
        $(".action").click(function() {
            let act = $(this).prop('id');
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                url: "{{ route('serialize-data') }}",
                method: "post",
                dataType: "TEXT",
                data: $("#form1").serialize(),
                success: function(res) {

                    let result = JSON.parse(res);
                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        url: `/Save/Estimate/${act}`,
                        dataType: "TEXT",
                        method: "post",
                        data: {
                            action: act,
                            emp_id: {{ session()->get('user')['crm_user_id'] }},
                            data: Base64Encode(res),
                            priceData: Base64Encode(JSON.stringify([])),
                            total: 0,
                            pot_id: result.pot_id,
                            project_name: result.project_name,
                            period: result[1].period,
                            tc: Base64Encode(JSON.stringify([])),
                        },
                        success: function(response) {
                            alert(response)
                            if (act == "save") {
                                window.location.href = "index.php?all";
                            }
                        }
                    })
                }
            })
        })
    </script>
@endsection
