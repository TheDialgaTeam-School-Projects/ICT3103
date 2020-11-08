<?php

namespace App\Services;

use App\Models\BankProfileOtp;
use Authy\AuthyApi;
use Illuminate\Contracts\Translation\Translator;

class AuthyService extends Service
{
    /**
     * @var AuthyApi
     */
    private $authyApi;

    /**
     * @var BankProfileOtp
     */
    private $bankProfileOtp;

    public function __construct(AuthyApi $authyApi, BankProfileOtp $bankProfileOtp, Translator $translator)
    {
        parent::__construct($translator);

        $this->authyApi = $authyApi;
        $this->bankProfileOtp = $bankProfileOtp;
    }

    /**
     * Request for sms authentication message.
     *
     * @param int $bankProfileId
     * @param bool $force
     * @param string|null $reason
     * @return bool true if request for sms authentication is success, else false.
     */
    public function requestSms(int $bankProfileId, bool $force = false, string &$reason = null): bool
    {
        if (!$this->bankProfileOtp->isRequestOtpAvailable($bankProfileId, $duration) && $force) {
            // User has requested otp just recently and is trying to force sending sms again.
            $reason = $this->trans_choice('common.otp.request_timeout', $duration);
            return false;
        }

        // Requesting sms is available.
        $this->bankProfileOtp->incrementLastRequestDateTime($bankProfileId);
        $request = $this->authyApi->requestSms($this->bankProfileOtp->getAuthyId($bankProfileId), ['force' => $force ? 'true' : 'false']);

        if (!$request->ok()) {
            $reason = $this->__('common.otp.service_error');
            return false;
        }

        return true;
    }

    public function verifyToken(int $bankProfileId, string $token, string &$reason = null): bool
    {
        if ($this->bankProfileOtp->isServingTimeout($bankProfileId, $duration)) {
            // User is serving a timeout from spamming too much otp requests.
            $reason = $this->trans_choice('common.lockout.otp', $duration);
            return false;
        }

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
