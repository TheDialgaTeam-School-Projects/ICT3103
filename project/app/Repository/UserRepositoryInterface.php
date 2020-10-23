<?php

namespace App\Repository;

use App\Models\User;

interface UserRepositoryInterface extends UserCrudRepositoryInterface, UserOtpCrudRepositoryInterface
{
    public function logUserSession(string $ipAddress, User $user = null): bool;
}
