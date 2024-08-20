<div class="except content-header bg-transparent">
    <div class="except container-fluid bg-transparent">
        <div class="except row mb-2 bg-transparent">
            <div class="except col-sm-6 v">

            </div>
            <div class="except col-sm-6 bg-transparent">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item "><a href="/">{{ __('Home') }}</a></li>
                    @foreach ($array as $name => $path)
                        <li class="breadcrumb-item">
                            <a class="{{ $loop->last ? 'text-muted' : '' }}"
                                href="{{ $path }}">{{ $name }}</a>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</div>
