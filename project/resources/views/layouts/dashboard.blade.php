@extends('layouts.home')

@section('content')
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="{{ App\Helpers\Helper::route('dashboard.bank_account_list') }}">
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
                    <a class="nav-link" href="{{ App\Helpers\Helper::route('dashboard.bank_account_list') }}">
                        Home <span class="sr-only">(current)</span>
                    </a>
                </li>
            </ul>
            <div class="btn-group">
                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                    <i class="fas fa-user-circle"></i> {{ $username }}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ App\Helpers\Helper::route('user_authentication.logout') }}">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>
@endsection
