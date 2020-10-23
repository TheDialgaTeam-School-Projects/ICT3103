<?php

namespace App\Repository;

use App\Models\User;

interface UserCrudRepositoryInterface
{
    public function createUser(string $username, string $password, string $firstName, string $lastName, $dateOfBirth): User;

    public function createBulkUsers(array $users): void;

    public function findUserById(int $id): ?User;

    public function findUserByUsername(string $username): ?User;

    public function isUserInTimeout(User $user = null): bool;

    public function incrementUserFailedCount(User $user = null): bool;

    public function resetUserFailedCount(User $user = null): bool;
}
