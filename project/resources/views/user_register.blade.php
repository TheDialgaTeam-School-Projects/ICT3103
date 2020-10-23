@extends('layouts.home')

@section('title', 'Register Account')

@section('content')
    <div class="register-container">
        <form class="register-form p-3 rounded" method="post" action="{{ route('user_registration.register') }}">
            <p>Register for iBanking account:</p>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name"
                           name="first_name" placeholder="First Name" maxlength="255" required
                           aria-describedby="@error('first_name') first_name_feedback @enderror"
                           value="{{ old('first_name') }}"/>
                    @error('first_name')
                    <div id="first_name_feedback" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                           name="last_name" placeholder="Last Name" maxlength="255" required
                           aria-describedby="@error('last_name') last_name_feedback @enderror"
                           value="{{ old('last_name') }}"/>
                </div>
                @error('last_name')
                <div id="last_name_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="date_of_birth">Date Of Birth</label>
                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth"
                       name="date_of_birth" required
                       aria-describedby="@error('date_of_birth') date_of_birth_feedback @enderror"
                       value="{{ old('date_of_birth') }}"/>
                @error('date_of_birth')
                <div id="date_of_birth_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username"
                       name="username" placeholder="Username" aria-describedby="username_hint" minlength="3"
                       maxlength="255" required value="{{ old('username') }}"/>
                <small id="username_hint" class="form-text text-muted">
                    Your username must be 3-255 characters long, and must not contain spaces or special characters.
                </small>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                       name="password" placeholder="Password" aria-describedby="password_hint" minlength="8" required
                       value="{{ old('password') }}">
                <small id="password_hint" class="form-text text-muted">
                    Your password must be at least 8 characters long, contains at least one capital letter, one number,
                    and one special characters.
                </small>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" class="form-control @error('password_confirm') is-invalid @enderror"
                       id="password_confirm" name="password_confirm" placeholder="Confirm Password"
                       aria-describedby="@error('password_confirm') password_confirm_feedback @enderror" minlength="8"
                       required value="{{ old('password_confirm') }}">
                @error('password_confirm')
                <div id="password_confirm_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <a class="btn btn-primary" href="{{ route('user_authentication.index') }}" role="button">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn btn-primary">Register</button>
            @csrf
        </form>
    </div>
@endsection
