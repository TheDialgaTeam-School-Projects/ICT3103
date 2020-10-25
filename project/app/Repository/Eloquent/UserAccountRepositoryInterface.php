<?php

namespace App\Repository\Eloquent;

use App\Models\UserAccount;

interface UserAccountRepositoryInterface
{
    /**
     * Create a user account.
     *
     * @param string $username Username.
     * @param string $password Password.
     * @param string $bankProfileId Target bank profile id.
     * @return bool true if insert query is successful, else false.
     */
    public function createUserAccount(string $username, string $password, string $bankProfileId): bool;

    /**
     * Create a list of user accounts.
     *
     * @param array $userAccounts List of user account to insert.
     * @return bool true if insert query is successful, else false.
     */
    public function createUserAccountInBulk(array $userAccounts): bool;

    /**
     * Find a user account.
     *
     * @param string $username Username to find.
     * @return UserAccount|null user account model or null if not found.
     */
    public function findUserAccount(string $username): ?UserAccount;

    /**
     * Check if the user account is created with the provided bank profile id.
     *
     * @param string $bankProfileId Bank profile id.
     * @return bool true if the user account is created, else false.
     */
    public function isUserAccountCreated(string $bankProfileId): bool;

    /**
     * Get if the user is currently serving a timeout.
     *
     * @param UserAccount|null $userAccount [optional] Target user account. null for current logged in user.
     * @return bool true if the user is serving a timeout, else false.
     */
    public function isUserServingTimeout(UserAccount $userAccount = null): bool;

    /**
     * Increment user failed count by 1.
     *
     * @param UserAccount|null $userAccount [optional] Target user account. null for current logged in user.
     * @return bool true if update query is successful, else false.
     */
    public function incrementUserFailedCount(UserAccount $userAccount = null): bool;

    /**
     * Reset user failed count to 0.
     *
     * @param UserAccount|null $userAccount [optional] Target user account. null for current logged in user.
     * @return bool true if update query is successful, else false.
     */
    public function resetUserFailedCount(UserAccount $userAccount = null): bool;
}
