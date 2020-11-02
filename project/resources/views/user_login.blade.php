@extends('layouts.home')

@section('title', 'Login')

@section('content')
    <div class="login-container">
        <form class="login-form p-3 rounded" method="post" action="{{ route('user_authentication.login_post') }}">
            <i class="fas fa-piggy-bank mb-2" style="width: 72px; height: 72px; color: red"></i>
            <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
            @if (isset($alertType, $alertMessage))
                <x-alert :alert-type="$alertType" :alert-message="$alertMessage"></x-alert>
            @endif
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username"
                       name="username" placeholder="Username" required
                       aria-describedby="@error('username') username_feedback @enderror"
                       value="{{ old('username') }}"/>
                @error('username')
                <div id="username_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                       name="password" placeholder="Password" required
                       aria-describedby="@error('password') password_feedback @enderror"
                       value="{{ old('password') }}"/>
                @error('password')
                <div id="password_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            <a class="btn btn-lg btn-primary btn-block" href="{{ route('user_registration.register_identify_get') }}"
               role="button">Register</a>
            @csrf
        </form>
    </div>
@endsection
