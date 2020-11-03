<?php

namespace App\Models;

use App\Helpers\Helper;
use Carbon\Carbon;
use Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @mixin IdeHelperUserAccount
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

    public function createAccount(string $username, string $password, int $bankProfileId)
    {
        return $this->create([
            'username' => $username,
            'password' => Hash::make($password),
            'bank_profile_id' => $bankProfileId,
        ]);
    }

    public function getUserAccount(string $username): UserAccount
    {
        return $this->find($username);
    }

    public function getBankProfileId(string $username): int
    {
        return $this->find($username)->bank_profile_id;
    }

    public function isServingTimeout(string $username, int &$duration = null): bool
    {
        $userAccount = $this->find($username);
        if (!isset($userAccount, $userAccount->password_reset_datetime)) {
            $duration = 0;
            return false;
        }

        $resetTimestamp = $userAccount->password_reset_datetime;
        $currentTimeStamp = Carbon::now();

        if ($currentTimeStamp->lessThan($resetTimestamp)) {
            $duration = $resetTimestamp->getTimestamp() - $currentTimeStamp->getTimestamp();
            return true;
        } else {
            $duration = 0;
            return false;
        }
    }

    public function incrementFailedCount(string $username): bool
    {
        $userAccount = $this->find($username);
        if (!isset($userAccount)) return false;

        $userAccount->password_failed_count++;

        if ($userAccount->password_failed_count >= Helper::getConfig()->get('lockout.user_account.max_attempt')) {
            $userAccount->password_failed_count = 0;
            $userAccount->password_reset_datetime = Carbon::now()->addMinutes(Helper::getConfig()->get('lockout.user_account.lockout_duration'));
        }

        return $userAccount->save();
    }

    public function resetFailedCount(string $username): bool
    {
        $userAccount = $this->find($username);
        if (!isset($userAccount)) return false;

        $userAccount->password_failed_count = 0;
        return $userAccount->save();
    }
}
