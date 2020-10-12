<?php

namespace teamwork\service;

use Exception;

class CsrfTokenService
{
    /** @var string Csrf token key for the session. */
    private const CSRF_TOKEN_KEY = 'CSRF_TOKENS';

    /** @var string[] Csrf token */
    private array $csrfToken;

    public function __construct()
    {
        if (!isset($_SESSION[self::CSRF_TOKEN_KEY])) {
            $_SESSION[self::CSRF_TOKEN_KEY] = [];
            $this->csrfToken = [];
        } else {
            $this->csrfToken = $_SESSION[self::CSRF_TOKEN_KEY];
        }
    }

    /**
     * Get csrf token.
     * @param string $sessionKey Session key used to store csrf token.
     * @param bool $regenerate Whether to regenerate the csrf token.
     * @return string Csrf token.
     */
    public function getCsrfToken(string $sessionKey, bool $regenerate = true): string
    {
        if ($regenerate || empty($this->csrfToken[$sessionKey])) {
            $this->generateCsrfToken($sessionKey);
        }

        return $this->csrfToken[$sessionKey];
    }

    /**
     * Generate a new csrf token.
     * @param string $sessionKey Session key used to store csrf token.
     */
    private function generateCsrfToken(string $sessionKey): void
    {
        try {
            $value = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            $value = bin2hex(md5(time()));
        }

        $this->csrfToken[$sessionKey] = $value;
        $_SESSION[self::CSRF_TOKEN_KEY][$sessionKey] = $value;
    }

    /**
     * Validate if the csrf token is valid or not.
     * @param string $sessionKey Session key stored for csrf token.
     * @param string $csrfToken Csrf token to verify.
     * @return bool true if the csrf token match, else false.
     */
    public function isCsrfTokenValid(string $sessionKey, string $csrfToken): bool
    {
        return hash_equals($this->csrfToken[$sessionKey], $csrfToken);
    }
}
