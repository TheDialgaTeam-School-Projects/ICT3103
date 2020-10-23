<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int user_account_id
 * @property int authy_id
 * @property ?Carbon last_request
 * @property int failed_count
 * @property ?Carbon reset_datetime
 * @property User user
 */
class UserOtp extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "user_otp";

    protected $fillable = [
        'user_account_id',
        'authy_id',
    ];

    protected $casts = [
        'last_request' => 'datetime',
        'reset_datetime' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_account_id');
    }
}
