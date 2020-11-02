<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\BankProfileOtp;
use Authy\AuthyApi;

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

    public function __construct(AuthyApi $authyApi, BankProfileOtp $bankProfileOtp)
    {
        $this->authyApi = $authyApi;
        $this->bankProfileOtp = $bankProfileOtp;
    }

    public function requestSms(int $bankProfileId, bool $force = false, string &$reason = null): bool
    {
        if (!$this->bankProfileOtp->isRequestOtpAvailable($bankProfileId, $duration)) {
            $reason = Helper::__('registration.user_verify_timeout', ['seconds' => $duration]);
            return false;
        }

        if ($this->bankProfileOtp->isServingTimeout($bankProfileId, $duration)) {
            $reason = Helper::__('registration.user_verify_timeout', ['seconds' => $duration]);
            return false;
        }

        // Requesting Sms is available.
        $this->bankProfileOtp->incrementLastRequestDateTime($bankProfileId);
        $request = $this->authyApi->requestSms($this->bankProfileOtp->getAuthyId($bankProfileId), ['force' => $force ? 'true' : 'false']);

        if (!$request->ok()) {
            $reason = print_r($request->errors());
            return false;
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
