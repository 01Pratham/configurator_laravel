@extends('layouts.admin')

@section('main')
    @include('admin.components.content-header', ['array' => $content_header])
    <div class="row except p-3">
        <div class="col-md-4 except">
            @include('components.search-box', ['searchableClass' => $searchable['class']])
        </div>
    </div>

    @include('admin.components.data-table', [
        'Header' => $table_head,
        'Data' => $table_body,
        'searchable' => $searchable,
        'exceptional_keys' => $exceptional_keys ?? null,
    ])

    {{ $table_body->onEachSide(1)->links() }}
@endsection
