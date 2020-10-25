<?php

namespace App\Repository\Eloquent;

use App\Models\UserAccount;

interface UserSessionRepositoryInterface
{
    /**
     * Log user session.
     *
     * @param string $ipAddress Client ip address.
     * @param UserAccount|null $userAccount [optional] Target user account. null for current logged in user.
     * @return bool true if insert query is successful, else false.
     */
    public function logUserSession(string $ipAddress, UserAccount $userAccount = null): bool;
}
