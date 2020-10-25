@extends('layouts.home')

@section('title', 'Register Account')

@section('content')
    <div class="register-container">
        <form class="register-form p-3 rounded" method="post" action="{{ route('user_registration.verify') }}">
            <p>Register for iBanking account:</p>
            <h4 class="text-center">Step 1: Verification</h4>
            @if (isset($alertType, $alertMessage))
                <x-alert :alert-type="$alertType" :alert-message="$alertMessage"></x-alert>
            @endif
            <div class="form-group">
                <label for="bank_profile_id">Bank Profile Id</label>
                <input type="text" class="form-control @error('bank_profile_id') is-invalid @enderror"
                       id="bank_profile_id" name="bank_profile_id" placeholder="Bank Profile Id" maxlength="20"
                       required aria-describedby="@error('bank_profile_id') bank_profile_id_feedback @enderror"
                       value="{{ old('bank_profile_id') }}"/>
                @error('bank_profile_id')
                <div id="bank_profile_id_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small id="bank_profile_id_hint" class="form-text text-muted">
                    Enter your bank profile id which is given by the administrator when making your iBanking account.
                </small>
            </div>
            <div class="form-group">
                <label for="identification_id">Identification Id</label>
                <input type="text" class="form-control @error('identification_id') is-invalid @enderror"
                       id="identification_id" name="identification_id" placeholder="Identification Id" maxlength="255"
                       required aria-describedby="@error('identification_id') identification_id_feedback @enderror"
                       value="{{ old('identification_id') }}"/>
                @error('identification_id')
                <div id="identification_id_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small id="identification_id_hint" class="form-text text-muted">
                    Enter your identification id which you have given to the administrator when making your iBanking account.
                </small>
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
                <small id="date_of_birth_hint" class="form-text text-muted">
                    Enter your date of birth which you have given to the administrator when making your iBanking account.
                </small>
            </div>
            <a class="btn btn-primary" href="{{ route('user_authentication.login_index') }}" role="button">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn btn-primary">Verify</button>
            @csrf
        </form>
    </div>
@endsection
