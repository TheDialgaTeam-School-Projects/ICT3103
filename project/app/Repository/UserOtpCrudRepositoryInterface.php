<?php

namespace App\Repository;

use App\Models\User;

interface UserOtpCrudRepositoryInterface
{
    public function registerOtpToUser(int $authyId, User $user = null): bool;

    public function isOtpRegistered(User $user = null): bool;

    public function isOtpInTimeout(User $user = null): bool;

    public function getAuthyId(User $user = null): ?int;

    public function incrementOtpFailedCount(User $user = null): bool;

    public function resetOtpFailedCount(User $user = null): bool;
}
