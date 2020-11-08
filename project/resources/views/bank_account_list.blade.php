@extends('layouts.dashboard')

@section('title', 'User Dashboard')

@section('content')
    @parent
    <div class="container p-4" style="background-color: white;">
        @if (isset($alertType, $alertMessage))
            <x-alert :alert-type="$alertType" :alert-message="$alertMessage"></x-alert>
        @endif
        <h2>Your Bank Accounts</h2>
        <hr/>
        <div class="row">
            @foreach($bankAccounts as $bankAccount)
                <div class="col-sm-6">
                    <div class="card m-3">
                        <div class="card-body">
                            <h5 class="card-title">Bank Account ({{ $bankAccount->id }})</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{ ucfirst($bankAccount->account_type) }} Account</h6>
                            <p class="card-text">Balance: {{ sprintf("$%.2f", $bankAccount->balance) }}</p>
                            <a href="{{ App\Helpers\Helper::route('dashboard.bank_account_transaction', ['id' => $bankAccount->id]) }}" class="btn btn-primary">Check</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
