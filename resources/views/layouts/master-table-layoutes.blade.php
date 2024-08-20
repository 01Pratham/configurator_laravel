@extends('layouts.main-layout')

@section('main')
    @include('components.content-header', ['array' => $content_header])
    <div class="row except p-3">
        <div class="col-md-4 except">
            @include('components.search-box', ['searchableClass' => $searchable['class']])
        </div>

        <div class="ml-auto except">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#MyModal">
                Create Rate Card
            </button>
            @include('components.modal')
        </div>
    </div>

    @include('components.data-table', [
        'Header' => $table_head,
        'Data' => $table_body,
        'searchable' => $searchable,
        'exceptional_keys' => $exceptional_keys ?? null,
    ])
@endsection
