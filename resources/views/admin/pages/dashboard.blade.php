@extends('layouts.admin')

@section('main')
    @include('components.content-header', ['array' => ['Admin' => route('AdminDashboard')]])

    @if (!is_null($data))
        {{ $data }}
    @endif
@endsection
