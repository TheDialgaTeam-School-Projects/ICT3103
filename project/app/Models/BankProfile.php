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
        'identification_id',
        'date_of_birth',
        'name',
        'email',
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

    /**
     * Find the bank profile by identification id.
     *
     * @param string $identificationId User identification id.
     * @return BankProfile|null bank profile object if it exist, else null.
     */
    public function findByIdentificationId(string $identificationId): ?BankProfile
    {
        return $this->firstWhere('identification_id', $identificationId);
    }

    /**
     * Get bank profile id.
     *
     * @param string $identificationId User identification id.
     * @return int|null bank profile id.
     */
    public function getBankProfileId(string $identificationId): ?int
    {
        $bankProfile = $this->findByIdentificationId($identificationId);
        return isset($bankProfile) ? $bankProfile->id : null;
    }

    /**
     * Check if the bank profile is valid.
     *
     * @param string $identificationId User identification id.
     * @param string $dateOfBirth User date of birth.
     * @return bool true if the bank profile matches, else false.
     */
    public function isValidBankProfile(string $identificationId, string $dateOfBirth): bool
    {
        $bankProfile = $this->findByIdentificationId($identificationId);
        return isset($bankProfile) && $bankProfile->date_of_birth->isSameAs('Y-m-d', $dateOfBirth);
    }

    /**
     * Check if the user account is created.
     *
     * @param int $id
     * @return bool true if the user account is created, else false.
     */
    public function isUserAccountCreated(int $id): bool
    {
        $bankProfile = $this->find($id);
        return isset($bankProfile, $bankProfile->userAccount);
    }

    public function isBankAccountExist(int $bankProfileId, string $id): bool
    {
        $bankProfile = $this->find($bankProfileId);
        return $bankProfile->bankAccounts->contains('id', $id);
    }
}
