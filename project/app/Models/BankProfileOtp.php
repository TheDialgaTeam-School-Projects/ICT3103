<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model for bank profile otp table.
 *
 * @package App\Models
 * @property int id
 * @property int authy_id
 * @property bool authy_is_verified
 * @property ?CarbonInterface authy_last_request
 * @property int authy_failed_count
 * @property ?CarbonInterface authy_reset_datetime
 * @property string bank_profile_id
 * @property BankProfile bankProfile
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
        'authy_is_verified' => false,
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
        'authy_is_verified' => 'boolean',
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
}
