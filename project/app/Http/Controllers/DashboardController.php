<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankAccountTransferFormRequest;
use App\Models\BankAccount;
use App\Models\BankProfile;
use App\Models\BankTransaction;
use App\Models\UserAccount;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
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
        /** @var BankAccount $bankAccountFrom */
        $bankAccountFrom = $bankProfile->find($bankProfileId)->bankAccounts()->where('id', $id)->first();

        if ($formInputs['amount'] > $bankAccountFrom->balance) {
            $this->flashAlertMessage('error', 'Insufficient balance to transfer.');
            return $this->route('dashboard.bank_account_transfer_get', ['id' => $id]);
        }

        $bankAccountFrom->balance = $bankAccountFrom->balance - $formInputs['amount'];
        $bankAccountFrom->transactions->add(new BankTransaction([
            'transaction_type' => 'debit',
            'amount' => $formInputs['amount'],
            'transaction_timestamp' => Carbon::now(),
            'bank_account_id' => $bankAccountFrom->id,
        ]));
        $bankAccountFrom->push();

        $bankAccountTo = $bankAccount->find($formInputs['bank_account_id_to']);
        $bankAccountTo->balance = $bankAccountTo->balance + $formInputs['amount'];
        $bankAccountTo->transactions->add(new BankTransaction([
            'transaction_type' => 'credit',
            'amount' => $formInputs['amount'],
            'transaction_timestamp' => Carbon::now(),
            'bank_account_id' => $bankAccountTo->id,
        ]));
        $bankAccountTo->push();

        $this->flashAlertMessage('success', 'Transfer has been made.');
        return $this->route('dashboard.bank_account_transaction', ['id' => $id]);
    }
}
