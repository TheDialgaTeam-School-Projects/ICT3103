<?php

namespace App\Repository\Eloquent;

interface BankAccountRepositoryInterface
{
    /**
     * Create a list of bank account.
     *
     * @param array $bankAccounts List of bank account to insert.
     * @return bool true if insert query is successful, else false.
     */
    public function createBankAccountInBulk(array $bankAccounts): bool;
}
