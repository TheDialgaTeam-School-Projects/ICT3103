@extends('layouts.home')

@section('title', 'Register Account')

@section('content')
    <div class="register-container">
        <form class="register-form p-3 rounded"
              method="post" action="{{ \App\Helpers\Helper::route('user_registration.register_create_post') }}">
            <h4 class="text-center">Step 3: Registration</h4>
            @if (isset($alertType, $alertMessage))
                <x-alert :alert-type="$alertType" :alert-message="$alertMessage"></x-alert>
            @endif
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror"
                       id="username" name="username"
                       placeholder="Username"
                       required minlength="3" maxlength="255"
                       aria-describedby="username_hint"
                       value="{{ old('username') }}"/>
                @error('username')
                <div id="username_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small id="username_hint" class="form-text text-muted">
                    Your username must be 3-255 characters long, and must not contain spaces or special characters.
                </small>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                       id="password" name="password"
                       placeholder="Password"
                       required minlength="8"
                       aria-describedby="password_hint"
                       value="{{ old('password') }}">
                @error('password')
                <div id="password_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small id="password_hint" class="form-text text-muted">
                    Your password must be at least 8 characters long, contains at least one capital letter, one number,
                    and one special characters.
                </small>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" class="form-control @error('password_confirm') is-invalid @enderror"
                       id="password_confirm" name="password_confirm"
                       placeholder="Confirm Password"
                       required minlength="8"
                       aria-describedby="@error('password_confirm') password_confirm_feedback @enderror"
                       value="{{ old('password_confirm') }}">
                @error('password_confirm')
                <div id="password_confirm_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <a class="btn btn-primary" role="button"
               href="{{ \App\Helpers\Helper::route('user_authentication.login_get') }}">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
            <button type="submit" class="btn btn-primary">Register</button>
            @csrf
        </form>
    </div>
@endsection
