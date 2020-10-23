<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserAuthenticationController;
use App\Http\Controllers\UserRegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route that can be accessed by guest and redirects to dashboard if logged in.
Route::middleware('guest')->group(function () {
    Route::get('/', [UserAuthenticationController::class, 'index'])->name('user_authentication.index');
    Route::post('/', [UserAuthenticationController::class, 'login'])->name('user_authentication.login');

    Route::get('/register', [UserRegistrationController::class, 'index'])->name('user_registration.index');
    Route::post('/register', [UserRegistrationController::class, 'register'])->name('user_registration.register');
});

Route::get('/login/check', [UserAuthenticationController::class, 'login_check'])->name('user_authentication.login_check');

// Route that requires user to be logged in or it redirect back to index.
Route::middleware('auth')->group(function () {
    Route::get('/login/2fa', [UserAuthenticationController::class, 'login_2fa'])->name('user_authentication.login_2fa');
    Route::post('/login/2fa', [UserAuthenticationController::class, 'login_2fa_verify'])->name('user_authentication.login_2fa_verify');

    Route::get('/register/2fa', [UserRegistrationController::class, 'register_2fa'])->name('user_registration.register_2fa');
    Route::post('/register/2fa', [UserRegistrationController::class, 'register_2fa_verify'])->name('user_registration.register_2fa_verify');

    Route::get('/logout', [UserAuthenticationController::class, 'logout'])->name('user_authentication.logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
});
