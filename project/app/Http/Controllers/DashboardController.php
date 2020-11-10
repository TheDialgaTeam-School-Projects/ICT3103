<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankAccountTransferConfirmFormRequest;
use App\Http\Requests\BankAccountTransferFormRequest;
use App\Models\BankAccount;
use App\Models\BankProfile;
use App\Models\BankTransaction;
use App\Models\UserAccount;
use App\Services\AuthyService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public const BANK_ACCOUNT_ID_FROM_SESSION_KEY = "BANK_ACCOUNT_ID_FROM_SESSION_KEY";
    public const BANK_ACCOUNT_ID_TO_SESSION_KEY = "BANK_ACCOUNT_ID_TO_SESSION_KEY";
    public const AMOUNT_SESSION_KEY = "AMOUNT_SESSION_KEY";

    public function bank_account_list(UserAccount $userAccount)
    {
        return $this->view('bank_account_list', [
            'bankAccounts' => $userAccount->getBankAccounts(Auth::id()),
        ]);
    }

    public function view(string $view, array $data = [], $mergeData = []): View
    {
        return parent::view($view, $data, [
            'username' => Auth::id()
        ]);
    }

    public function bank_account_transaction(UserAccount $userAccount, BankProfile $bankProfile, BankAccount $bankAccount, string $id)
    {
        $bankProfileId = $userAccount->getBankProfileId(Auth::id());

        if (!$bankProfile->isBankAccountExist($bankProfileId, $id)) {
            $this->flashAlertMessage('error', 'Invalid bank account accessed.');
            return $this->route('dashboard.bank_account_list');
        }

        $bankTransactions = $bankAccount->find($id)->transactions;

        return $this->view('bank_account_transaction', [
            'id' => $id,
            'bankAccount' => $bankAccount->find($id),
            'transactions' => $bankTransactions,
        ]);
    }

    public function bank_account_transfer_get(UserAccount $userAccount, BankProfile $bankProfile, string $id)
    {
        $bankProfileId = $userAccount->getBankProfileId(Auth::id());

        if (!$bankProfile->isBankAccountExist($bankProfileId, $id)) {
            $this->flashAlertMessage('error', 'Invalid bank account accessed.');
            return $this->route('dashboard.bank_account_list');
        }

        return $this->view('bank_account_transfer', [
            'id' => $id,
            'balance' => $bankProfile->find($bankProfileId)->bankAccounts()->where('id', $id)->first()->balance,
        ]);
    }

    public function bank_account_transfer_post(BankAccountTransferFormRequest $request, UserAccount $userAccount, BankProfile $bankProfile, BankAccount $bankAccount, string $id)
    {
        $bankProfileId = $userAccount->getBankProfileId(Auth::id());

        if (!$bankProfile->isBankAccountExist($bankProfileId, $id)) {
            $this->flashAlertMessage('error', 'Invalid bank account accessed.');
            return $this->route('dashboard.bank_account_list');
        }

        $formInputs = $request->validated();

        if ($formInputs['bank_account_id_to'] === $id) {
            $this->flashAlertMessage('error', 'You cannot transfer to yourself.');
            return $this->route('dashboard.bank_account_transfer_get', ['id' => $id]);
        }

        $bankAccountFrom = $bankAccount->find($id);

        if ($formInputs['amount'] > $bankAccountFrom->balance) {
            $this->flashAlertMessage('error', 'Insufficient balance to transfer.');
            return $this->route('dashboard.bank_account_transfer_get', ['id' => $id]);
        }

        $this->getSession()->put([
            self::BANK_ACCOUNT_ID_FROM_SESSION_KEY => $id,
            self::BANK_ACCOUNT_ID_TO_SESSION_KEY => $formInputs['bank_account_id_to'],
            self::AMOUNT_SESSION_KEY => $formInputs['amount'],
        ]);

        return $this->route('dashboard.bank_account_transfer_confirm_get', ['id' => $id]);
    }

    public function bank_account_transfer_confirm_get(Request $request, UserAccount $userAccount, BankAccount $bankAccount, AuthyService $authyService, string $id)
    {
        if (App::isLocal()) {
            // If application is local development environment, skip 2FA as this consume API cost for debugging.
            $bankAccountIdFrom = $this->getSession()->get(self::BANK_ACCOUNT_ID_FROM_SESSION_KEY);
            $bankAccountIdTo = $this->getSession()->get(self::BANK_ACCOUNT_ID_TO_SESSION_KEY);
            $amount = $this->getSession()->get(self::AMOUNT_SESSION_KEY);

            $bankAccountFrom = $bankAccount->find($bankAccountIdFrom);
            $bankAccountFrom->balance = $bankAccountFrom->balance - $amount;
            $bankAccountFrom->transactions->add(new BankTransaction([
                'transaction_type' => 'debit',
                'amount' => $amount,
                'transaction_timestamp' => Carbon::now(),
                'bank_account_id' => $bankAccountFrom->id,
            ]));
            $bankAccountFrom->push();

            $bankAccountTo = $bankAccount->find($bankAccountIdTo);
            $bankAccountTo->balance = $bankAccountTo->balance + $amount;
            $bankAccountTo->transactions->add(new BankTransaction([
                'transaction_type' => 'credit',
                'amount' => $amount,
                'transaction_timestamp' => Carbon::now(),
                'bank_account_id' => $bankAccountTo->id,
            ]));
            $bankAccountTo->push();

            $this->flashAlertMessage('success', 'Transfer has been made.');
            $this->getSession()->forget([
                self::BANK_ACCOUNT_ID_FROM_SESSION_KEY,
                self::BANK_ACCOUNT_ID_TO_SESSION_KEY,
                self::AMOUNT_SESSION_KEY
            ]);

            return $this->route('dashboard.bank_account_transaction', ['id' => $id]);
        }

        // Else on the production environment, do verify 2FA before continuing.
        $bankProfileId = $userAccount->getBankProfileId(Auth::id());
        $isSmsForced = $request->input('force_sms', false);

        // Request for sms message.
        if (!$authyService->requestSms($bankProfileId, $isSmsForced, $reason)) {
            $this->flashAlertMessage('error', $reason);
        }

        return $this->view('bank_account_transfer_confirm', [
            'id' => $id,
        ]);
    }

    public function bank_account_transfer_confirm_post(BankAccountTransferConfirmFormRequest $request, UserAccount $userAccount, BankAccount $bankAccount, AuthyService $authyService, string $id)
    {
        return $this->getGlobalLockoutViewOrContinue('bank_account_transfer_confirm', function () use ($request, $userAccount, $bankAccount, $authyService, $id) {
            $bankProfileId = $userAccount->getBankProfileId(Auth::id());
            $formInputs = $request->validated();

            if (!$authyService->verifyToken($bankProfileId, $formInputs['two_factor_token'])) {
                // User failed to verify a valid token.
                $this->incrementGlobalLockoutFailedCount('bank_account_transfer_confirm');
                $this->flashAlertMessage('error', $this->__('registration.user_verify_failed'));
                return $this->view('bank_account_transfer_confirm', ['id' => $id]);
            }

            // User has successfully verified and should now continue the transaction
            $this->resetGlobalLockoutFailedCount('bank_account_transfer_confirm');

            $bankAccountIdFrom = $this->getSession()->get(self::BANK_ACCOUNT_ID_FROM_SESSION_KEY);
            $bankAccountIdTo = $this->getSession()->get(self::BANK_ACCOUNT_ID_TO_SESSION_KEY);
            $amount = $this->getSession()->get(self::AMOUNT_SESSION_KEY);

            $bankAccountFrom = $bankAccount->find($bankAccountIdFrom);
            $bankAccountFrom->balance = $bankAccountFrom->balance - $amount;
            $bankAccountFrom->transactions->add(new BankTransaction([
                'transaction_type' => 'debit',
                'amount' => $amount,
                'transaction_timestamp' => Carbon::now(),
                'bank_account_id' => $bankAccountFrom->id,
            ]));
            $bankAccountFrom->push();

            $bankAccountTo = $bankAccount->find($bankAccountIdTo);
            $bankAccountTo->balance = $bankAccountTo->balance + $amount;
            $bankAccountTo->transactions->add(new BankTransaction([
                'transaction_type' => 'credit',
                'amount' => $amount,
                'transaction_timestamp' => Carbon::now(),
                'bank_account_id' => $bankAccountTo->id,
            ]));
            $bankAccountTo->push();

            $this->flashAlertMessage('success', 'Transfer has been made.');
            $this->getSession()->forget([
                self::BANK_ACCOUNT_ID_FROM_SESSION_KEY,
                self::BANK_ACCOUNT_ID_TO_SESSION_KEY,
                self::AMOUNT_SESSION_KEY
            ]);

            return $this->route('dashboard.bank_account_transaction', ['id' => $id]);
        });
    }
}
