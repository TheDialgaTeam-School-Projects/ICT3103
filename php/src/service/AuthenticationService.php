<?php

namespace teamwork\service;

use teamwork\model\UserAccountModel;

class AuthenticationService
{
    /** @var string Session token key. */
    private const SESSION_TOKEN_KEY = 'SESSION_USER';

    /** @var MySqlService Database object. */
    private MySqlService $db;

    /**
     * AuthenticationService constructor.
     * @param MySqlService $db Database object.
     */
    public function __construct(MySqlService $db)
    {
        $this->db = $db;
    }

    /**
     * Attempt to login.
     * @param string $username Username.
     * @param string $password Password.
     * @return bool true if user is able to login successfully or false.
     */
    public function login(string $username, string $password): bool
    {
        // If username or password is empty, we can't login.
        if (empty($username) || empty($password)) return false;

        $query = $this->db->query('select password from user_account where username = ?', $username);
        if (!$query) return false;

        /** @var UserAccountModel|null $result */
        $result = $query->fetch_object(UserAccountModel::class);
        $query->close();

        if (!$result || !password_verify($password, $result->getPassword())) return false;

        $this->db->query('insert into logger(username, logged_datetime) VALUE (?, ?)', $username, date('Y-m-d H:i:s'));

        $_SESSION[self::SESSION_TOKEN_KEY] = $username;

        return true;
    }

    /**
     * Destroy the current session.
     * @return bool true on success or false on failure.
     */
    public function logout(): bool
    {
        return session_destroy();
    }

    /**
     * Register an account.
     * @param array $formInputs Form data.
     * @param string $error Error message.
     * @return bool true on success or false on failure.
     */
    public function register(array $formInputs, string &$error): bool
    {
        if (count($formInputs) === 0) {
            $error = 'Form is empty.';
            return false;
        }

        // Validate fields.
        $username = $formInputs['username'];

        if (empty($username) || strlen($username) < 3 || strlen($username) > 255) {
            $error = 'Username is empty or do not conform to the requirement.';
            return false;
        }

        $usernameChars = str_split($username);

        foreach ($usernameChars as $usernameChar) {
            if (preg_match('/[^A-Za-z0-9]/', $usernameChar)) {
                $error = 'Username do not conform to the requirement.';
                return false;
            }
        }

        /** @var string $password */
        $password = $formInputs['password'];

        if (empty($password) || strlen($password) < 8) {
            $error = 'Password is empty or do not conform to the requirement.';
            return false;
        }

        $caps = false;
        $number = false;
        $specialChar = false;
        $passwordChars = str_split($password);

        foreach ($passwordChars as $passwordChar) {
            if (preg_match('/[A-Z]/', $passwordChar)) {
                $caps = true;
                continue;
            } else if (preg_match('/[0-9]/', $passwordChar)) {
                $number = true;
            } else if (preg_match('/[^A-Za-z0-9]/', $passwordChar)) {
                $specialChar = true;
            }
        }

        if (!$caps || !$number || !$specialChar) {
            $error = 'Password do not conform to the requirement.';
            return false;
        }

        $mobileNumber = $formInputs['mobile'];

        if (empty($mobileNumber)) {
            $error = 'Mobile number is empty or do not conform to the requirement.';
            return false;
        }

        $firstName = $formInputs['firstname'];

        if (empty($firstName)) {
            $error = 'First name is empty or do not conform to the requirement.';
            return false;
        }

        $lastName = $formInputs['lastname'];

        if (empty($lastName)) {
            $error = 'Last name is empty or do not conform to the requirement.';
            return false;
        }

        $dob = $formInputs['dob'];

        if (empty($dob)) {
            $error = 'Date of birth is empty or do not conform to the requirement.';
            return false;
        }

        // Ensure account is not created beforehand.
        $query = $this->db->query('select count(*) from user_account where username = ?', $username);

        if (!$query) {
            $error = 'Database connection error.';
            return false;
        }

        try {
            if ($query->fetch_array(MYSQLI_NUM)[0] > 0) {
                $error = 'Username has already been used.';
                return false;
            }
        } finally {
            $query->close();
        }

        // Commit new account!
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        if (!$passwordHash) {
            $error = 'Expected error had occurred.';
            return false;
        }

        $this->db->query('insert into user_account(username, password, phone, first_name, last_name, date_of_birth) VALUE (?, ?, ?, ?, ?, ?)', $username, $passwordHash, $mobileNumber, $firstName, $lastName, $dob);

        return true;
    }

    /**
     * Check if the user is logged in.
     * @return bool true if user is logged in, else false.
     */
    public function isLoggedIn(): bool
    {
        return isset($_SESSION[self::SESSION_TOKEN_KEY]);
    }
}
