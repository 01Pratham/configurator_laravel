@php
    $files = [
        'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback',
        'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback',
        'https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css',
        'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css',
        'assets/dist/css/adminlte.min.css',
        'assets/dist/css/adminlte.min.css',
        'assets/plugins/fontawesome-free/css/all.min.css',
        'assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css',
        'assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css',
        'assets/plugins/jqvmap/jqvmap.min.css',
        'assets/dist/css/style.css',
        'assets/dist/css/loader.css',
    ];
@endphp

@foreach ($files as $file)
    <link rel="stylesheet" href="{{ asset($file) ?? $file }}">
@endforeach
