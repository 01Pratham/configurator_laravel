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
                <button class="Next-Btn" name="proceed" formtarget="_blank">Proceed <i
                        class="px-2 py-2  fa fa-angle-double-right"></i></button>
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
                @endphp

                @if (!request()->has('edit_id') && empty($potQuery->id))
                    <div class="except sub-button shadow btn btn-outline-success action" id="save">
                        <i class="except icons fa fa-save"></i>
                    </div>
                @else
                    <div class="except sub-button shadow btn btn-outline-info action" title="Update" id="update">
                        <i class="except icons fa fa-files-o" title="Update"></i>
                    </div>
                @endif

            </div>
        </form>
    </div>

    <script></script>
@endsection
