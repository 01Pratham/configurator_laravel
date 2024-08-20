@extends('layouts.main-layout')

@section('main')
    @include('components.content-header', ['array' => ['Dashboard' => route('Dashboard')]])
    <section class="except content">
        <div class="except container-fluid">

            <div class="except row">
                <div class="except col-lg-3 col-6">
                    <div class="except small-box bg-info" onclick="">
                        <div class="except inner">
                            <h3 class="text-info">.</h3>
                            <p class="except">Rate Cards</p>
                        </div>
                        <div class="except icon">
                            <i class="fa fa-tachometer-alt"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            More info
                            <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="except col-lg-3 col-6">
                    <div class="except small-box bg-success" onclick="location.href = '{{ route('SavedEstimates') }}'">
                        <div class="except inner">
                            <h3>{{ session()->get('user')['estimate_count'] }}</h3>

                            <p class="except">Saved Quotation</p>
                        </div>
                        <div class="except icon">
                            <i class="fa fa-folder-open "></i>
                        </div>
                        <a href="{{ route('SavedEstimates') }}" class="small-box-footer">
                            More info
                            <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="except col-lg-3 col-6">
                    <div class="except small-box bg-secondary" onclick="location.href = '{{ route('CreateNew') }}'">
                        <div class="except inner">
                            <h3 class="text-secondary">.</h3>
                            <p class="except">Create New</p>
                        </div>
                        <div class="except icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <a href="{{ route('CreateNew') }}" class="small-box-footer">
                            More info
                            <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="except col-lg-3 col-6">
                    <div class="except small-box bg-danger" onclick="location.href = '{{ route('Users') }}'">
                        <div class="except inner">
                            <h3 class = "text-danger">.</h3>
                            <p class="except">Users </p>
                        </div>
                        <div class="except icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="{{ route('Users') }}" class="small-box-footer">
                            More info
                            <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function() {
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                url: `/Session/mode/${localStorage.getItem("mode")}`,
                method: 'GET',
                success: function(res) {
                    // console.log(res)
                }
            })
        })
    </script>
@endsection
