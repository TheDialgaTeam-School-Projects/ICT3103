@extends('layouts.home')

@section('title', 'Bank Account ' . $id)

@section('content')
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="{{ route('dashboard.bank_account_list') }}">
            <i class="fas fa-piggy-bank" style="width: 30px; height: 30px;"></i>
            <span class="navbar-brand mb-0 h1">ICT3x03 Bank Demo</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('dashboard.bank_account_list') }}">Home <span class="sr-only">(current)</span></a>
                </li>
            </ul>
            <div class="btn-group">
                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                    <i class="fas fa-user-circle"></i> {{ $username }}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#">My Profile</a>
                    <a class="dropdown-item" href="#">Change Password</a>
                    <a class="dropdown-item" href="{{ route('user_registration.register_2fa_get') }}">Update 2FA Token</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('user_authentication.logout') }}">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container p-4" style="background-color: white;">
        <h2>Bank Account {{ $id }}</h2>
        <hr />
        <span>Quick Actions: </span>
        <span><a class="btn btn-primary" href="#" role="button">Transfer</a></span>
        <hr />
        <h4>List of transactions:</h4>
        <hr />
    </div>
@endsection