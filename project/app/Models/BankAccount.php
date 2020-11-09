<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperBankAccount
 */
class BankAccount extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "bank_account";

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
        'balance',
        'account_type',
        'bank_profile_id',
    ];

    /**
     * Get the bank profile record associated with the bank account.
     *
     * @return BelongsTo
     */
    public function bankProfile()
    {
        return $this->belongsTo(BankProfile::class, 'bank_profile_id');
    }

    /**
     * Get the bank transactions record associated with the bank account.
     *
     * @return HasMany
     */
    public function transactions()
    {
        return $this->hasMany(BankTransaction::class, 'bank_account_id');
    }

    public function isBankAccountExist($id): bool
    {
        $bankAccount = $this->find($id);
        return isset($bankAccount);
    }
}
