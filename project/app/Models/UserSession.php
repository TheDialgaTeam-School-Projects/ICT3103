<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int user_account_id
 * @property string ip_address
 * @property Carbon last_logged_in
 * @property User user
 */
class UserSession extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "user_last_session";

    protected $fillable = [
        'user_account_id',
        'ip_address',
        'last_logged_in',
    ];

    protected $hidden = [
        'ip_address',
    ];

    protected $casts = [
        'ip_address' => 'string',
        'last_logged_in' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_account_id');
    }
}
