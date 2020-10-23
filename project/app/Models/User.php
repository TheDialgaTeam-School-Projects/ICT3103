<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;

/**
 * @property int id
 * @property string username
 * @property string password
 * @property string first_name
 * @property string last_name
 * @property Carbon date_of_birth
 * @property int failed_count
 * @property ?Carbon reset_datetime
 * @property Collection sessions
 * @property ?UserOtp otp
 */
class User extends Authenticatable
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "user_account";

    protected $fillable = [
        'username',
        'password',
        'first_name',
        'last_name',
        'date_of_birth',
    ];

    protected $hidden = [
        'password',
        'first_name',
        'last_name',
        'date_of_birth',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'reset_datetime' => 'datetime',
    ];

    public function sessions()
    {
        return $this->hasMany(UserSession::class, 'user_account_id');
    }

    public function otp()
    {
        return $this->hasOne(UserOtp::class, 'user_account_id');
    }
}
