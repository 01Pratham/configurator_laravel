@extends('layouts.login')

@section('box')
    <div class="panel-heading except">
        <h3 class="pt-3 font-weight-bold">{{ __('Reset Password') }}</h3>
    </div>
    <div class="panel-body p-3 except">
        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
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
            </div>
            <div class="form-group py-1 pb-2">
                <div class="input-field">
                    <span class="fas fa-lock px-2" id="eye" onclick="ToogleClass($(this).prop('id') , 'pass')"></span>
                    <input type="password" placeholder="Password" id="pass" name='password'
                        class="@error('password') is-invalid @enderror" required autocomplete="new-password">
                    <!--    <span class="far fa-eye-slash " id="eye"></span>-->
                </div>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group py-1 pb-2">
                <div class="input-field">
                    <span class="fas fa-lock px-2" id="eye" onclick="ToogleClass($(this).prop('id') , 'pass')"></span>
                    <input type="password" placeholder="Password" required id="password-confirm"
                        name='password_confirmation' class="@error('password_confirmation') is-invalid @enderror" required
                        autocomplete="new-password">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-3" name="login_btn" id="login_btn">
                {{ __('Reset Password') }}
            </button>
        </form>
    </div>
@endsection
