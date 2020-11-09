@extends('layouts.dashboard')

@section('title', 'Bank Transfer')

@section('content')
    @parent
    <div class="container dashboard-container p-4">
        @if (isset($alertType, $alertMessage))
            <x-alert :alert-type="$alertType" :alert-message="$alertMessage"></x-alert>
        @endif
        <h2>Bank Transfer</h2>
        <hr/>
        <form method="post"
              action="{{ \App\Helpers\Helper::route('dashboard.bank_account_transfer_post', ['id' => $id]) }}">
            <div class="form-group">
                <label for="bank_account_id_from">Bank Account (From):</label>
                <input type="text" class="form-control"
                       id="bank_account_id_from" name="bank_account_id_from"
                       disabled
                       value="{{ $id }}"/>
            </div>
            <div class="form-group">
                <label for="bank_account_id_to">Bank Account (To):</label>
                <input type="text" class="form-control @error('bank_account_id_to') is-invalid @enderror"
                       id="bank_account_id_to" name="bank_account_id_to"
                       placeholder="Bank Account (To)"
                       required maxlength="255"
                       aria-describedby="bank_account_id_to_hint"
                       value="{{ old('bank_account_id_to') }}"/>
                @error('bank_account_id_to')
                <div id="bank_account_id_to_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small id="bank_account_id_to_hint" class="form-text text-muted">
                    Target bank account number to transfer to.
                </small>
            </div>
            <div class="form-group">
                <label for="amount">Amount: (Current Balance: {{ sprintf('$%.2f', $balance) }})</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                    </div>
                    <input type="text" class="form-control @error('amount') is-invalid @enderror"
                           id="amount" name="amount"
                           placeholder="0.00"
                           required maxlength="255"
                           aria-label="Amount" aria-describedby="amount_hint"
                           value="{{ old('amount') }}">
                </div>
                @error('amount')
                <div id="amount_feedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small id="amount_hint" class="form-text text-muted">
                    Amount to transfer. (Ensure that you have enough money to transfer or transaction may fail).
                </small>
            </div>
            <a class="btn btn-primary" role="button"
               href="{{ \App\Helpers\Helper::route('dashboard.bank_account_transaction', ['id' => $id]) }}">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn btn-primary">Transfer</button>
            @csrf
        </form>
    </div>
@endsection
