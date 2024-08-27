@extends('layouts.login')

@section('box')
    <div class="panel-heading except">
        <h3 class="pt-3 font-weight-bold">{{ __('Reset Password') }}</h3>
    </div>
    <div class="panel-body p-3 except">

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group py-2">
                <div class="input-field">
                    <span class="fas fa-envelope px-2"></span>
                    <input type="email" placeholder="email" class="@error('email') is-invalid @enderror " required
                        name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                </div>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                @if (session('status'))
                    <span class="valid-msg">
                        <strong>{{ session('status') }}</strong>
                    </span>
                @endif
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-3" name="login_btn" id="login_btn">
                {{ __('Send Password Reset Link') }}
            </button>
        </form>
    </div>
@endsection
