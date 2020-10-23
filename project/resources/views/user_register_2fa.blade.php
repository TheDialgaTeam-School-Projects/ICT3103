@extends('layouts.home')

@section('title', 'Register Two-factor authentication')

@section('content')
    <div class="register-container">
        <form class="register-form p-3 rounded" method="post" action="{{ route('user_registration.register_2fa_verify') }}">
            @if (isset($alertType, $alertMessage))
                @if ($alertType === 'error')
                    <div class="alert alert-danger" role="alert">{{ $alertMessage }}</div>
                @elseif ($alertType === 'warning')
                    <div class="alert alert-warning" role="alert">{{ $alertMessage }}</div>
                @endif
            @endif
            <div class="form-group">
                <label for="email_address">Email address</label>
                <input type="email" class="form-control @error('email_address') is-invalid @enderror" id="email_address"
                       name="email_address" placeholder="Email address" required
                       aria-describedby="@error('email_address') email_address_feedback @enderror"
                       value="{{ old('email_address') }}"/>
                <small class="form-text text-muted">
                    Email address is only used for registering the secure token. It will not be stored in the server.
                    <br/>
                    You can use <a href="https://authy.com/install">Authy Application</a> to manage your tokens.
                </small>
                @error('email_address')
                <div id="email_address_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="mobile_number">Mobile Number (+65)</label>
                <input type="tel" class="form-control @error('mobile_number') is-invalid @enderror" id="mobile_number"
                       name="mobile_number" placeholder="Mobile Number (+65)" required
                       aria-describedby="@error('mobile_number') mobile_number_feedback @enderror"
                       value="{{ old('mobile_number') }}"/>
                <small class="form-text text-muted">
                    Please enter your mobile number that you wish to register your Two-factor authentication.
                </small>
                @error('mobile_number')
                <div id="mobile_number_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <a class="btn btn-primary" href="{{ route('user_authentication.logout') }}" role="button">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
            <button type="submit" class="btn btn-primary">Register</button>
            @csrf
        </form>
    </div>
@endsection
