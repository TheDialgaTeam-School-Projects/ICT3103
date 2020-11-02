<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\BankProfile;
use App\Models\BankProfileOtp;
use App\Models\UserAccount;
use Authy\AuthyApi;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * @var BankProfile
     */
    private $bankProfile;

    /**
     * @var BankProfileOtp
     */
    private $bankProfileOtp;

    /**
     * @var BankAccount
     */
    private $bankAccount;

    /**
     * @var UserAccount
     */
    private $userAccount;

    /**
     * @var AuthyApi
     */
    private $authyApi;

    public function __construct(BankProfile $bankProfile, BankProfileOtp $bankProfileOtp, BankAccount $bankAccount, UserAccount $userAccount, AuthyApi $authyApi)
    {
        $this->bankProfile = $bankProfile;
        $this->bankProfileOtp = $bankProfileOtp;
        $this->bankAccount = $bankAccount;
        $this->userAccount = $userAccount;
        $this->authyApi = $authyApi;
    }

    /**
     * Execute seeder.
     */
    public function run()
    {
        $bankAccounts = [
            [
                'BankProfile' => [
                    'identification_id' => 'jianmingyong',
                    'date_of_birth' => Carbon::create(1998, 2, 24),
                    'name' => 'Yong Jian Ming',
                    'email' => 'jianming1993@gmail.com',
                ],
                'BankProfileOtp' => [
                    'mobile_number' => '90294382',
                ],
                'BankAccounts' => [
                    [
                        'id' => '0018527413',
                        'balance' => 1000,
                        'account_type' => 'savings',
                    ],
                ],
                'UserAccount' => [
                    'username' => 'jianmingyong',
                    'password' => '$2y$10$guILWksdshtFV13S2CT6/useh3AoxOcB.makekCrw07a/ZVvcBgH2',
                ],
            ],
            [
                'BankProfile' => [
                    'identification_id' => 'test',
                    'date_of_birth' => Carbon::create(1998, 2, 24),
                    'name' => 'Yong Jian Ming',
                    'email' => 'jianming1993@gmail.com',
                ],
                'BankProfileOtp' => [
                    'mobile_number' => '90294382',
                ],
                'BankAccounts' => [
                    [
                        'id' => '0018527414',
                        'balance' => 1000,
                        'account_type' => 'savings',
                    ],
                ],
            ]
        ];

        foreach ($bankAccounts as $bankAccount) {
            $bankProfileId = $this->bankProfile->create($bankAccount['BankProfile'])->id;

            if (array_key_exists('BankProfileOtp', $bankAccount)) {
                $authyId = $this->authyApi->registerUser($bankAccount['BankProfile']['email'], $bankAccount['BankProfileOtp']['mobile_number'], 65)->id();
                $this->bankProfileOtp->create(['authy_id' => $authyId, 'bank_profile_id' => $bankProfileId]);
            }

            if (array_key_exists('BankAccounts', $bankAccount)) {
                foreach ($bankAccount['BankAccounts'] as $account) {
                    $this->bankAccount->create(array_merge($account, ['bank_profile_id' => $bankProfileId]));
                }
            }

            if (array_key_exists('UserAccount', $bankAccount)) {
                $this->userAccount->create(array_merge($bankAccount['UserAccount'], ['bank_profile_id' => $bankProfileId]));
            }
        }
    }
}
