@extends('layouts.main-layout')

@section('main')
    @include('components.content-header', [
        'array' => ['Create New' => route('SavedEstimates')],
    ])



    <div class="except container mt-2">
        <div class="Errors except"></div>
        <section id="create_Main" class="light rounded">
            <form class="row px-3 p-3 g-2 py-4" action="{{ route('Estimate') }}" method="post">
                @csrf
                <input type="hidden" name="edit_id" value="{{ $id ?? '' }}">
                <div class="except px-2 col-md-6">
                    <label for="inputEmail4" class="form-label">
                        <i class="fa fa-info-circle px-2  " title="Enter 5 Chracters only."></i>
                        POT ID :
                    </label>
                    <input type="number" class="form-control" id="pot_id"
                        value="{{ $Data?->pot_id == 0 ? '' : $Data?->pot_id }}" min="1000" max='99999' name="pot_id"
                        placeholder="Enter POT ID " style="border: none; border-bottom: 1px solid ; border-radius:0;"
                        required>
                </div>
                <div class="except px-2 col-md-6 ">
                    <label for="inputEmail4" class="form-label">Project Name : </label>
                    <input type="Text" class="form-control" id="project_name"
                        value="{{ $Data?->project_name == 0 ? '' : $Data?->project_name }}" min="1000" max='9999'
                        name="project_name" placeholder="Enter Project Name "
                        style="border: none; border-bottom: 1px solid ; border-radius:0;" required>
                </div>
                <div class="except px-2 col-md-6  py-4">
                    <label for="inputEmail4" class="form-label">Qoutation Name : </label>
                    <input type="Text" class="form-control" id="quotation_name"
                        value="{{ $Data?->quotation_name == 0 ? '' : $Data?->quotation_name }}" min="1000"
                        max='9999' name="quotation_name" placeholder="Enter Quotation Name"
                        style="border: none; border-bottom: 1px solid ; border-radius:0;" required>
                </div>
                <div class="except px-2 col-md-6  py-4">
                    <label for="pice_list" class="form-label">Price List</label>
                    <select class="form-control" name="product_list" id="pice_list"
                        style="border: none; border-bottom: 1px solid ; border-radius:0;" required>
                        @foreach ($priceLists as $list)
                            <option value="{{ $list['id'] }}"
                                @if ($Data?->price_list == $list['id']) {{ __('selected') }} @endif>{{ $list['rate_card_name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="except px-2 col-12 d-flex justify-content-center">
                    <button role='button' id="Next-Btn" class="Next-Btn">Next</button>
                </div>
            </form>
        </section>
    </div>
@endsection
