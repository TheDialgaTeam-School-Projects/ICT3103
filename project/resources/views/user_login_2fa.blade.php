@extends('layouts.home')

@section('title', 'Two-factor authentication')

@section('content')
    <div class="register-container">
        <form class="register-form p-3 rounded"
              method="post" action="{{ route('user_authentication.login_2fa_post') }}">
            @if (isset($alertType, $alertMessage))
                <x-alert :alert-type="$alertType" :alert-message="$alertMessage"></x-alert>
            @endif
            <div class="text-center">
                <i class="fas fa-key" style="width: 72px; height: 72px; color: lightslategrey"></i>
                <h1 class="h4 mb-3 font-weight-normal">
                    {{ __('registration.user_verify_two_factor_authentication') }}
                </h1>
            </div>
            <div class="form-group">
                <label for="two_factor_token">{{ __('registration.user_verify_two_factor_token_label') }}</label>
                <input type="text" class="form-control @error('2fa_token') is-invalid @enderror"
                       id="two_factor_token" name="two_factor_token"
                       placeholder="{{ __('registration.user_verify_two_factor_token_placeholder') }}"
                       minlength="6" maxlength="6" required
                       aria-describedby="@error('2fa_token') two_factor_token_feedback @enderror"/>
                @error('two_factor_token')
                <div id="two_factor_token_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
                <a href="{{ route('user_registration.register_verify_get', ['force_sms' => true]) }}">
                    <small>{{ __('registration.user_verify_request_button') }}</small>
                </a>
            </div>
            <button type="submit" class="btn btn-primary btn-block">{{ __('common.next') }}</button>
            <a class="btn btn-primary btn-block" role="button"
               href="{{ route('user_authentication.logout') }}">
                <i class="fas fa-arrow-left"></i> {{ __('common.back_to_login') }}
            </a>
            @csrf
        </form>
    </div>
@endsection
