@extends('layouts.dashboard')

@section('title', 'Bank Account ' . $id)

@section('content')
    @parent
    <div class="container dashboard-container p-4">
        @if (isset($alertType, $alertMessage))
            <x-alert :alert-type="$alertType" :alert-message="$alertMessage"></x-alert>
        @endif
        <h2>Bank Account {{ $id }}</h2>
        <hr/>
        <p class="card-text">Total Balance: {{ sprintf("$%.2f", $bankAccount->balance) }}</p>
        <span>Quick Actions: </span>
        <span><a class="btn btn-primary"
                 href="{{ \App\Helpers\Helper::route('dashboard.bank_account_transfer_get', ['id' => $id]) }}"
                 role="button">Transfer</a></span>
        <hr/>
        <h4>List of transactions:</h4>
        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Timestamp</th>
                <th scope="col">Transaction Type</th>
                <th scope="col">Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->id }}</td>
                    <td>{{ $transaction->transaction_timestamp }}</td>
                    <td>{{ $transaction->transaction_type }}</td>
                    <td>{{ $transaction->amount }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
