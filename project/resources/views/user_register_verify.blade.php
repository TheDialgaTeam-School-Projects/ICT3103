@extends('layouts.home')

@section('title', __('registration.user_verify_title'))

@section('content')
    <div class="register-container">
        <form class="register-form p-3 rounded"
              method="post" action="{{ \App\Helpers\Helper::route('user_registration.register_verify_post') }}">
            <h4 class="text-center">{{ \App\Helpers\Helper::__('registration.user_verify_header') }}</h4>
            @if (isset($alertType, $alertMessage))
                <x-alert :alert-type="$alertType" :alert-message="$alertMessage"></x-alert>
            @endif
            <div class="text-center">
                <i class="fas fa-key" style="width: 72px; height: 72px; color: lightslategrey"></i>
                <h1 class="h4 mb-3 font-weight-normal">
                    {{ \App\Helpers\Helper::__('registration.user_verify_two_factor_authentication') }}
                </h1>
            </div>
            <div class="form-group">
                <label for="two_factor_token">
                    {{ \App\Helpers\Helper::__('registration.user_verify_two_factor_token_label') }}
                </label>
                <input type="text" class="form-control @error('2fa_token') is-invalid @enderror"
                       id="two_factor_token" name="two_factor_token"
                       placeholder="{{ \App\Helpers\Helper::__('registration.user_verify_two_factor_token_placeholder') }}"
                       minlength="6" maxlength="6" required
                       aria-describedby="@error('2fa_token') two_factor_token_feedback @enderror"/>
                @error('two_factor_token')
                <div id="two_factor_token_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
                <a href="{{ route('user_registration.register_verify_get', ['force_sms' => true]) }}">
                    <small>{{ \App\Helpers\Helper::__('registration.user_verify_request_button') }}</small>
                </a>
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                {{ \App\Helpers\Helper::__('common.verify') }}
            </button>
            <a class="btn btn-primary btn-block" role="button"
               href="{{ \App\Helpers\Helper::route('user_registration.register_identify_get') }}">
                <i class="fas fa-arrow-left"></i> {{ \App\Helpers\Helper::__('common.back') }}
            </a>
            @csrf
        </form>
    </div>
@endsection
