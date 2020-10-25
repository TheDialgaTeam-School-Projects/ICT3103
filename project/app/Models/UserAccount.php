<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Model for user account table.
 *
 * @package App\Models
 * @property string username
 * @property string password
 * @property int password_failed_count
 * @property ?CarbonInterface password_reset_datetime
 * @property string bank_profile_id
 * @property BankProfile bankProfile
 * @property Collection sessions
 */
class UserAccount extends Authenticatable
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "user_account";

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'username';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'username',
        'password',
        'bank_profile_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'password_failed_count' => 0,
        'password_reset_datetime' => null,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'password_reset_datetime' => 'datetime',
    ];

    /**
     * Get the bank profile record associated with the user account.
     *
     * @return BelongsTo
     */
    public function bankProfile()
    {
        return $this->belongsTo(BankProfile::class, 'bank_profile_id');
    }

    /**
     * Get the user session record associated with the user account.
     *
     * @return HasMany
     */
    public function sessions()
    {
        return $this->hasMany(UserSession::class, 'username', 'username');
    }
}
