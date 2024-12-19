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
        function ToogleClass(toggleIconId, inputFieldId) {
            const $toggleIcon = $("#" + toggleIconId);
            const $inputField = $("#" + inputFieldId);

            const isLocked = $toggleIcon.hasClass('fa-lock');

            $toggleIcon.toggleClass('fa-lock', !isLocked).toggleClass('fa-unlock', isLocked);
            $inputField.attr("type", isLocked ? "text" : "password");
        }
        $(document).ready(function() {
            $("body").removeAttr("style")
        })
    </script>
@endsection
