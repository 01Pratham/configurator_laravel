@extends('app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/dist/css/login.css') }}">
    <div class="container page-box except">
        <div class="d-flex justify-content-center except">
            <div class="panel border bg-white except">
                @yield('box')
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $("body").removeAttr("style")
        })
    </script>
@endsection
