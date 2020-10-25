<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model for bank transaction table.
 *
 * @package App\Models
 * @property int id
 * @property string transaction_type
 * @property string amount
 * @property CarbonInterface transaction_timestamp
 * @property string bank_account_id
 * @property BankAccount bankAccount
 */
class BankTransaction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "bank_transaction";

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
        'transaction_type',
        'amount',
        'transaction_timestamp',
        'bank_account_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'transaction_timestamp' => 'datetime',
    ];

    /**
     * Get the bank account record associated with the bank transaction.
     *
     * @return BelongsTo
     */
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }
}
