@extends('layouts.main-layout')

@section('main')
    <div class="except container mt-5">
        <div class="except card text-center">
            <div class="except card-header bg-{{ $status }}">
                {{ $msg }}
            </div>
            <div class=" card-body">
                <a href="{{ $redirect }}" class="btn btn-primary">Go Back</a>
            </div>
        </div>
    </div>
@endsection
