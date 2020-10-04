<?php

namespace teamwork\service;

use Exception;

class CsrfTokenService
{
    /** @var string[] Csrf token */
    private array $csrfToken;

    /**
     * Get csrf token.
     * @param string $sessionKey Session key used to store csrf token.
     * @param bool $regenerate Whether to regenerate the csrf token.
     * @return string Csrf token.
     */
    public function getCsrfToken(string $sessionKey, bool $regenerate = false): string
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
    public function generateCsrfToken(string $sessionKey): void
    {
        try {
            $this->csrfToken[$sessionKey] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            $this->csrfToken[$sessionKey] = bin2hex(md5(time()));
        }

        $_SESSION[$sessionKey] = $this->csrfToken[$sessionKey];
    }

    /**
     * Validate if the csrf token is valid or not.
     * @param string $sessionKey Session key stored for csrf token.
     * @param string $csrfToken Csrf token to verify.
     * @return bool true if the csrf token match, else false.
     */
    public function validateCsrfToken(string $sessionKey, string $csrfToken): bool
    {
        return hash_equals($_SESSION[$sessionKey], $csrfToken);
    }
}
