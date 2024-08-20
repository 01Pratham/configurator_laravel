@php
    $files = [
        'assets/plugins/jquery/jquery.min.js',
        'assets/plugins/jquery-ui/jquery-ui.min.js',
        'assets/dist/js/adminlte.js',
        'assets/dist/js/demo.js',
        'assets/plugins/bootstrap/js/bootstrap.bundle.min.js',
        'assets/dist/js/main.js',
        'assets/dist/js/jquery.serializeToJSON.min.js',
    ];
@endphp

@foreach ($files as $file)
    <script src="{{ asset($file) }}"></script>
@endforeach
