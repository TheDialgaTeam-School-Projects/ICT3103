@extends('layouts.home')

@section('title', __('registration.user_identify_title'))

@section('content')
    <div class="register-container">
        <form class="register-form p-3 rounded"
              method="post" action="{{ route('user_registration.register_identify_post') }}">
            <h4 class="text-center">{{ __('registration.user_identify_header') }}</h4>
            @if (isset($alertType, $alertMessage))
                <x-alert :alert-type="$alertType" :alert-message="$alertMessage"></x-alert>
            @endif
            <div class="form-group">
                <label for="identification_id">{{ __('registration.user_identify_identification_id_label') }}</label>
                <input type="text" class="form-control @error('identification_id') is-invalid @enderror"
                       id="identification_id" name="identification_id"
                       placeholder="{{__('registration.user_identify_identification_id_label')}}"
                       required maxlength="255"
                       aria-describedby="@error('identification_id') identification_id_feedback @enderror"
                       value="{{ old('identification_id') }}"/>
                @error('identification_id')
                <div id="identification_id_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small id="identification_id_hint" class="form-text text-muted">
                    {{ __('registration.user_identify_identification_id_hint') }}
                </small>
            </div>
            <div class="form-group">
                <label for="date_of_birth">{{ __('registration.user_identify_date_of_birth_label') }}</label>
                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                       id="date_of_birth" name="date_of_birth"
                       required max="{{ today()->toDateString() }}"
                       aria-describedby="@error('date_of_birth') date_of_birth_feedback @enderror"
                       value="{{ old('date_of_birth') }}"/>
                @error('date_of_birth')
                <div id="date_of_birth_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small id="date_of_birth_hint" class="form-text text-muted">
                    {{ __('registration.user_identify_date_of_birth_hint') }}
                </small>
            </div>
            <a class="btn btn-primary" href="{{ route('user_authentication.login_get') }}" role="button">
                <i class="fas fa-arrow-left"></i> {{ __('common.back_to_login') }}
            </a>
            <button type="submit" class="btn btn-primary">{{ __('common.next') }}</button>
            @csrf
        </form>
    </div>
@endsection
