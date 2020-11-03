<?php

namespace App\Services;

use App\Models\BankProfileOtp;
use Authy\AuthyApi;
use Illuminate\Contracts\Translation\Translator;

class AuthyService
{
    /**
     * @var AuthyApi
     */
    private $authyApi;

    /**
     * @var BankProfileOtp
     */
    private $bankProfileOtp;

    /**
     * @var Translator
     */
    private $translator;

    public function __construct(AuthyApi $authyApi, BankProfileOtp $bankProfileOtp, Translator $translator)
    {
        $this->authyApi = $authyApi;
        $this->bankProfileOtp = $bankProfileOtp;
        $this->translator = $translator;
    }

    public function requestSms(int $bankProfileId, bool $force = false, string &$reason = null): bool
    {
        $isRequestOtpAvailable = $this->bankProfileOtp->isRequestOtpAvailable($bankProfileId, $otpDuration);

        if (!$isRequestOtpAvailable && $force) {
            $reason = $this->translator->choice('otp.request_timeout', $otpDuration);
            return false;
        }

        if ($this->bankProfileOtp->isServingTimeout($bankProfileId, $duration)) {
            $reason = $this->translator->choice('lockout.message', $duration);
            return false;
        }

        if ($isRequestOtpAvailable) {
            // Requesting Sms is available.
            $this->bankProfileOtp->incrementLastRequestDateTime($bankProfileId);
            $request = $this->authyApi->requestSms($this->bankProfileOtp->getAuthyId($bankProfileId), ['force' => $force ? 'true' : 'false']);

            if (!$request->ok()) {
                $reason = print_r($request->errors(), true);
                return false;
            }
        }

        return true;
    }

    public function verifyToken(int $bankProfileId, string $token): bool
    {
        $authyId = $this->bankProfileOtp->getAuthyId($bankProfileId);

        if ($this->authyApi->verifyToken($authyId, $token)->ok()) {
            $this->bankProfileOtp->resetFailedCount($bankProfileId);
            return true;
        } else {
            $this->bankProfileOtp->incrementFailedCount($bankProfileId);
            return false;
        }
    }
}
