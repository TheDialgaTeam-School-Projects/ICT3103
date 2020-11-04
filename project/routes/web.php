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
    // Login
    Route::get('/', [UserAuthenticationController::class, 'login_get'])->name('user_authentication.login_get');
    Route::post('/', [UserAuthenticationController::class, 'login_post'])->name('user_authentication.login_post');

    // Register Step 1: Identify User
    Route::get('/register/identify', [UserRegistrationController::class, 'register_identify_get'])->name('user_registration.register_identify_get');
    Route::post('/register/identify', [UserRegistrationController::class, 'register_identify_post'])->name('user_registration.register_identify_post');

    // Route that requires bank_profile_id to be in session or redirect to identify user page.
    Route::middleware(sprintf('session:%s,user_registration.register_identify_get', UserRegistrationController::BANK_PROFILE_ID_SESSION_KEY))->group(function () {
        // Register Step 2: Verify User
        Route::get('/register/verify', [UserRegistrationController::class, 'register_verify_get'])->name('user_registration.register_verify_get');
        Route::post('/register/verify', [UserRegistrationController::class, 'register_verify_post'])->name('user_registration.register_verify_post');

        Route::middleware(sprintf('session:%s,user_registration.register_identify_get', UserRegistrationController::REGISTER_USER_VERIFIED_SESSION_KEY))->group(function () {
            // Register Step 3: Create Account
            Route::get('/register/create', [UserRegistrationController::class, 'register_create_get'])->name('user_registration.register_create_get');
            Route::post('/register/create', [UserRegistrationController::class, 'register_create_post'])->name('user_registration.register_create_post');
        });
    });
});

Route::get('/login/check', [UserAuthenticationController::class, 'login_check'])->name('user_authentication.login_check');

// Route that requires user to be logged in or it redirect back to index.
Route::middleware('auth')->group(function () {
    Route::get('/login/2fa', [UserAuthenticationController::class, 'login_2fa_get'])->name('user_authentication.login_2fa_get');
    Route::post('/login/2fa', [UserAuthenticationController::class, 'login_2fa_post'])->name('user_authentication.login_2fa_post');

    Route::get('/register/2fa', [UserRegistrationController::class, 'register_2fa_get'])->name('user_registration.register_2fa_get');
    Route::post('/register/2fa', [UserRegistrationController::class, 'register_2fa_post'])->name('user_registration.register_2fa_post');

    Route::get('/logout', [UserAuthenticationController::class, 'logout'])->name('user_authentication.logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
});
