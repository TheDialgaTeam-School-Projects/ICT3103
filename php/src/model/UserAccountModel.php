<?php

namespace teamwork\model;

/**
 * Class for user account table.
 * @package teamwork\model
 */
class UserAccountModel
{
    /** @var string Username. */
    private string $username;

    /** @var string Hash password. */
    private string $password;

    /** @var int Mobile number. */
    private int $phone;

    /** @var string First name. */
    private string $first_name;

    /** @var string Last name. */
    private string $last_name;

    /** @var string Data of birth. */
    private string $date_of_birth;

    /**
     * Get username.
     * @return string username.
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Get hashed password.
     * @return string hashed password.
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Get mobile number.
     * @return int mobile number.
     */
    public function getPhone(): int
    {
        return $this->phone;
    }

    /**
     * Get first name.
     * @return string first name.
     */
    public function getFirstName(): string
    {
        return $this->first_name;
    }

    /**
     * Get last name.
     * @return string last name.
     */
    public function getLastName(): string
    {
        return $this->last_name;
    }

    /**
     * Get date of birth.
     * @return string date of birth.
     */
    public function getDateOfBirth(): string
    {
        return $this->date_of_birth;
    }
}
