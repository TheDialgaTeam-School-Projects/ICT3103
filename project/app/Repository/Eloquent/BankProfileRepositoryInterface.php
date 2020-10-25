<?php

namespace App\Repository\Eloquent;

interface BankProfileRepositoryInterface
{
    /**
     * Create a list of bank profile.
     *
     * @param array $bankProfiles List of bank profile to insert.
     * @return bool true if insert query is successful, else false.
     */
    public function createBankProfileInBulk(array $bankProfiles): bool;

    /**
     * Check if the bank profile valid from user input.
     *
     * @param string $id Bank profile id.
     * @param string $identificationId Identification id.
     * @param string $dateOfBirth Date of birth.
     * @return bool true if the bank profile is valid, else false.
     */
    public function isBankProfileValid(string $id, string $identificationId, string $dateOfBirth): bool;
}
