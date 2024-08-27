@extends('layouts.login')

@section('box')
    <div class="panel-heading except">
        <h3 class="pt-3 font-weight-bold">{{ __('Login') }}</h3>
    </div>
    <div class="panel-body p-3 except">
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group py-2">
                <div class="input-field">
                    <span class="far fa-user p-2"></span>
                    <input type="text" placeholder="Username" class="@error('username') is-invalid @enderror" required
                        name='username' value="{{ old('username') }}" required autocomplete="username" autofocus>
                </div>
                @error('username')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group py-1 pb-2">
                <div class="input-field">
                    <span class="fas fa-lock px-2" id="eye" onclick="ToogleClass($(this).prop('id') , 'pass')"></span>
                    <input type="password" placeholder="Password" required id="pass" name='password'
                        class="@error('password') is-invalid @enderror" autocomplete="current-password">
                    <!--    <span class="far fa-eye-slash " id="eye"></span>-->
                </div>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-inline">
                <a href="/password/reset" id="forgot" class="font-weight-bold">Forgot password ?</a>
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-3" name="login_btn" id="login_btn">
                {{ __('Login') }}
            </button>
        </form>
    </div>
@endsection
