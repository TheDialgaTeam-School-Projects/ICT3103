@extends('layouts.home')

@section('title', 'Verify Two-factor authentication')

@section('content')
    <div class="register-container">
        <form class="register-form p-3 rounded" method="post"
              action="{{ route('user_authentication.login_2fa_verify') }}">
            <div class="text-center">
                <i class="fas fa-key" style="width: 72px; height: 72px; color: lightslategrey"></i>
                <h1 class="h4 mb-3 font-weight-normal">Two-factor authentication</h1>
            </div>
            @if (isset($alertType, $alertMessage))
                @if ($alertType === 'error')
                    <div class="alert alert-danger" role="alert">{{ $alertMessage }}</div>
                @endif
            @endif
            <div class="form-group">
                <label for="2fa_token">Enter Two-factor authentication code</label>
                <input type="text" class="form-control @error('2fa_token') is-invalid @enderror" id="2fa_token"
                       name="2fa_token" placeholder="6 digit authentication code" minlength="6"
                       maxlength="6" required aria-describedby="@error('2fa_token') 2fa_token_feedback @enderror"/>
                @error('2fa_token')
                <div id="2fa_token_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary btn-block">Verify</button>
            <a class="btn btn-primary btn-block" href="{{ route('user_authentication.logout') }}" role="button">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
            @csrf
        </form>
    </div>
@endsection
