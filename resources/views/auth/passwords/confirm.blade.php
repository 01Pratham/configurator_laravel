@extends('layouts.login')

@section('box')
    <div class="panel-heading except">
        <h3 class="pt-3 font-weight-bold">{{ __('Confirm Password') }}</h3>
    </div>
    <div class="panel-body p-3 except">
        <form action="{{ route('password.confirm') }}" method="POST">
            @csrf
            <div class="form-group py-1 pb-2">
                <div class="input-field">
                    <span class="fas fa-lock px-2" id="eye" onclick="ToogleClass($(this).prop('id') , 'pass')"></span>
                    <input type="password" placeholder="Password" required id="pass" name='password'
                        class="@error('password') is-invalid @enderror" autocomplete="current-password">
                </div>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            @if (Route::has('password.request'))
                <div class="form-inline">
                    <a href="{{ route('password.request') }}" id="forgot" class="font-weight-bold">
                        {{ __('Forgot Your Password?') }}
                    </a>
                </div>
            @endif
            <button type="submit" class="btn btn-primary btn-block mt-3" name="login_btn" id="login_btn">
                {{ __('Login') }}
            </button>
        </form>
    </div>
@endsection
