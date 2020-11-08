@extends('layouts.dashboard')

@section('title', 'Bank Account ' . $id)

@section('content')
    @parent
    <div class="container dashboard-container p-4">
        @if (isset($alertType, $alertMessage))
            <x-alert :alert-type="$alertType" :alert-message="$alertMessage"></x-alert>
        @endif
        <h2>Bank Account {{ $id }}</h2>
        <hr />
        <span>Quick Actions: </span>
        <span><a class="btn btn-primary" href="{{ \App\Helpers\Helper::route('dashboard.bank_account_transfer_get', ['id' => $id]) }}" role="button">Transfer</a></span>
        <hr />
        <h4>List of transactions:</h4>
        <hr />
    </div>
@endsection
