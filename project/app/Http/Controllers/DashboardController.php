<?php

namespace App\Http\Controllers;

use App\Models\BankProfile;
use App\Models\UserAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function view(string $view, array $data = [], $mergeData = []): View
    {
        return parent::view($view, $data, [
            'username' => Auth::id()
        ]);
    }

    public function bank_account_list(UserAccount $userAccount)
    {
        return $this->view('bank_account_list', [
            'bankAccounts' => $userAccount->getBankAccounts(Auth::id()),
        ]);
    }

    public function bank_account_transaction(UserAccount $userAccount, BankProfile $bankProfile, string $id)
    {
        $bankProfileId = $userAccount->getBankProfileId(Auth::id());

        if (!$bankProfile->isBankAccountExist($bankProfileId, $id)) {
            $this->flashAlertMessage('error', 'Invalid bank account accessed.');
            return $this->route('dashboard.bank_account_list');
        }

        return $this->view('bank_account_transaction', [
            'id' => $id,
        ]);
    }
}
