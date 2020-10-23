<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatabaseTable extends Migration
{
    public function up()
    {
        Schema::create('user_account', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->integer('failed_count')->default(0);
            $table->dateTime('reset_datetime')->nullable();
        });

        Schema::create('user_last_session', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_account_id')->constrained('user_account')->cascadeOnDelete()->cascadeOnUpdate();
            $table->ipAddress('ip_address');
            $table->dateTime('last_logged_in');
        });

        Schema::create('user_otp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_account_id')->constrained('user_account')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('authy_id');
            $table->dateTime('last_request')->nullable();
            $table->integer('failed_count')->default(0);
            $table->dateTime('reset_datetime')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_account');
        Schema::dropIfExists('user_last_session');
        Schema::dropIfExists('user_otp');
    }
}
