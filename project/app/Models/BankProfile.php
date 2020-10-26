<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin IdeHelperBankProfile
 */
class BankProfile extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "bank_profile";

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
        'id',
        'identification_id',
        'date_of_birth',
    ];

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'bank_profile_otp_id' => null,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date_of_birth' => 'datetime',
    ];

    /**
     * Get the bank profile otp record associated with the bank profile.
     *
     * @return HasOne
     */
    public function otp()
    {
        return $this->hasOne(BankProfileOtp::class, 'bank_profile_id');
    }

    /**
     * Get the bank accounts record associated with the bank profile.
     *
     * @return HasMany
     */
    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class, 'bank_profile_id');
    }

    /**
     * Get the user account record associated with the bank profile.
     *
     * @return HasOne
     */
    public function userAccount()
    {
        return $this->hasOne(UserAccount::class, 'bank_profile_id');
    }
}
