@extends('app')

@section('content')
    @include('admin.components.navbar')
    @include('admin.components.sidebar')
    <div class="except content-wrapper Main bg-transparent">
        <div id="loader" class="except">
            <div class="except cube-folding">
                <span class="except leaf1"></span>
                <span class="except leaf2"></span>
                <span class="except leaf3"></span>
                <span class="except leaf4"></span>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $("#loader").addClass("d-none")
            });
        </script>
        @yield('main')
    </div>
@endsection
