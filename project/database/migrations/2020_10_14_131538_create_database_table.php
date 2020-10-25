<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatabaseTable extends Migration
{
    public function up()
    {
        Schema::create('bank_profile', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('identification_id');
            $table->date('date_of_birth');
        });

        Schema::create('bank_profile_otp', function (Blueprint $table) {
            $table->id();
            $table->integer('authy_id');
            $table->boolean('authy_is_verified')->default(false);
            $table->dateTime('authy_last_request')->nullable();
            $table->integer('authy_failed_count')->default(0);
            $table->dateTime('authy_reset_datetime')->nullable();
            $table->string('bank_profile_id');
            $table->foreign('bank_profile_id')->references('id')->on('bank_profile')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('bank_account', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->decimal('balance');
            $table->string('account_type');
            $table->string('bank_profile_id');
            $table->foreign('bank_profile_id')->references('id')->on('bank_profile')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('bank_transaction', function (Blueprint $table) {
            $table->id();
            $table->enum('transaction_type', ['credit', 'debit']);
            $table->decimal('amount');
            $table->dateTime('transaction_timestamp');
            $table->string('bank_account_id');
            $table->foreign('bank_account_id')->references('id')->on('bank_account')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('user_account', function (Blueprint $table) {
            $table->string('username')->primary();
            $table->string('password');
            $table->integer('password_failed_count')->default(0);
            $table->dateTime('password_reset_datetime')->nullable();
            $table->string('bank_profile_id');
            $table->foreign('bank_profile_id')->references('id')->on('bank_profile')->restrictOnDelete()->cascadeOnUpdate();
        });

        Schema::create('user_last_session', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->ipAddress('ip_address');
            $table->dateTime('last_logged_in');
            $table->foreign('username')->references('username')->on('user_account')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_last_session');
        Schema::dropIfExists('user_account');
        Schema::dropIfExists('bank_transaction');
        Schema::dropIfExists('bank_account');
        Schema::dropIfExists('bank_profile_otp');
        Schema::dropIfExists('bank_profile');
    }
}
