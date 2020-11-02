<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperBankProfileOtp
 */
class BankProfileOtp extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "bank_profile_otp";

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
        'authy_id',
    ];

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'authy_last_request' => null,
        'authy_failed_count' => 0,
        'authy_reset_datetime' => null,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'authy_last_request' => 'datetime',
        'authy_reset_datetime' => 'datetime',
    ];

    /**
     * Get the bank profile record associated with the bank profile otp.
     *
     * @return BelongsTo
     */
    public function bankProfile()
    {
        return $this->belongsTo(BankProfile::class, 'bank_profile_id');
    }

    public function getAuthyId(int $bankProfileId): ?string
    {
        $bankProfileOtp = $this->findByBankProfileId($bankProfileId);
        if (!isset($bankProfileOtp)) return null;
        return $bankProfileOtp->authy_id;
    }

    public function findByBankProfileId($bankProfileId): ?BankProfileOtp
    {
        return $this->firstWhere('bank_profile_id', $bankProfileId);
    }

    public function isRequestOtpAvailable(int $bankProfileId, int &$duration = null): bool
    {
        $bankProfileOtp = $this->findByBankProfileId($bankProfileId);

        if (!isset($bankProfileOtp)) {
            $duration = 0;
            return false;
        }

        if (!isset($bankProfileOtp->authy_last_request)) {
            $duration = 0;
            return true;
        }

        $resetTimestamp = $bankProfileOtp->authy_last_request;
        $currentTimeStamp = Carbon::now();

        if ($currentTimeStamp->lessThan($resetTimestamp)) {
            $duration = $resetTimestamp->getTimestamp() - $currentTimeStamp->getTimestamp();
            return false;
        } else {
            $duration = 0;
            return true;
        }
    }

    public function isServingTimeout(int $bankProfileId, int &$duration = null): bool
    {
        $bankProfileOtp = $this->findByBankProfileId($bankProfileId);
        if (!isset($bankProfileOtp, $bankProfileOtp->authy_reset_datetime)) {
            $duration = 0;
            return false;
        }

        $resetTimestamp = $bankProfileOtp->authy_reset_datetime;
        $currentTimeStamp = Carbon::now();

        if ($currentTimeStamp->lessThan($resetTimestamp)) {
            $duration = $resetTimestamp->getTimestamp() - $currentTimeStamp->getTimestamp();
            return true;
        } else {
            $duration = 0;
            return false;
        }
    }

    public function incrementLastRequestDateTime(int $bankProfileId, int $minutes = 3): bool
    {
        $bankProfileOtp = $this->findByBankProfileId($bankProfileId);
        $bankProfileOtp->authy_last_request = Carbon::now()->addMinutes($minutes);
        return $bankProfileOtp->save();
    }

    public function incrementFailedCount(int $bankProfileId): bool
    {
        $bankProfileOtp = $this->findByBankProfileId($bankProfileId);
        if (!isset($bankProfileOtp)) return false;

        $bankProfileOtp->authy_failed_count++;

        if ($bankProfileOtp->authy_failed_count >= 5) {
            $bankProfileOtp->authy_failed_count = 0;
            $bankProfileOtp->authy_reset_datetime = Carbon::now()->addMinutes(5);
        }

        return $bankProfileOtp->save();
    }

    public function resetFailedCount(int $bankProfileId): bool
    {
        $bankProfileOtp = $this->findByBankProfileId($bankProfileId);
        if (!isset($bankProfileOtp)) return false;

        $bankProfileOtp->authy_failed_count = 0;
        return $bankProfileOtp->save();
    }
}
