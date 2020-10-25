<?php

namespace Database\Seeders;

use App\Repository\BulkCreatorRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Godruoyi\Snowflake\Snowflake;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BankAccountSeeder extends Seeder
{
    /**
     * @var Snowflake
     */
    private $snowflake;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var array
     */
    private $bankProfiles = [];

    /**
     * @var array
     */
    private $bankProfileOtpCollection = [];

    /**
     * @var array
     */
    private $bankAccounts = [];

    /**
     * @var array
     */
    private $userAccounts = [];

    /**
     * BankAccountSeeder constructor.
     *
     * @param Snowflake $snowflake
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(Snowflake $snowflake, UserRepositoryInterface $userRepository)
    {
        $this->snowflake = $snowflake;
        $this->userRepository = $userRepository;
    }

    /**
     * Execute seeder.
     */
    public function run()
    {
        $bankProfileId = $this->addBankProfile('jianmingyong', Carbon::create(1998, 2, 24), 258114310);
        $this->addUserSecure($bankProfileId, 'jianmingyong', '$2y$10$guILWksdshtFV13S2CT6/useh3AoxOcB.makekCrw07a/ZVvcBgH2');
        $this->addBankAccount($bankProfileId, '0018527413', 1000, 'savings');

        $this->userRepository->createBankProfileInBulk($this->bankProfiles);
        $this->userRepository->registerBankProfileOtpInBulk($this->bankProfileOtpCollection);
        $this->userRepository->createBankAccountInBulk($this->bankAccounts);
        $this->userRepository->createUserAccountInBulk($this->userAccounts);
    }

    /**
     * Add a new bank profile.
     *
     * @param string $identificationId Unique identification id.
     * @param CarbonInterface $dateOfBirth Date of birth.
     * @param int|null $authyId Authy id.
     * @return string bank profile id.
     */
    private function addBankProfile(string $identificationId, CarbonInterface $dateOfBirth, int $authyId = null): string
    {
        $randomId = $this->snowflake->id();

        $this->bankProfiles[] = [
            'id' => $randomId,
            'identification_id' => $identificationId,
            'date_of_birth' => $dateOfBirth,
        ];

        if ($authyId !== null) {
            $this->bankProfileOtpCollection[] = [
                'authy_id' => $authyId,
                'bank_profile_id' => $randomId,
            ];
        }

        return $randomId;
    }

    /**
     * Add a new bank account.
     *
     * @param string $bankProfileId Bank profile id.
     * @param string $accountNumber Bank account number.
     * @param float $balance Bank account balance.
     * @param string $accountType Bank account type.
     */
    private function addBankAccount(string $bankProfileId, string $accountNumber, float $balance, string $accountType)
    {
        $this->bankAccounts[] = [
            'id' => $accountNumber,
            'balance' => $balance,
            'account_type' => $accountType,
            'bank_profile_id' => $bankProfileId,
        ];
    }

    /**
     * Add a new user with secure hashed password.
     *
     * @param string $bankProfileId Bank profile id.
     * @param string $username Username.
     * @param string $hashedPassword Hashed password.
     */
    private function addUserSecure(string $bankProfileId, string $username, string $hashedPassword)
    {
        $this->userAccounts[] = [
            'username' => $username,
            'password' => $hashedPassword,
            'bank_profile_id' => $bankProfileId,
        ];
    }

    /**
     * Add a new user without secure hashed password.
     *
     * @param string $bankProfileId Bank profile id.
     * @param string $username Username.
     * @param string $password Raw password.
     * @see UserAccountSeeder::addUserSecure()
     */
    private function addUserInsecure(string $bankProfileId, string $username, string $password)
    {
        $this->userAccounts[] = [
            'username' => $username,
            'password' => Hash::make($password),
            'bank_profile_id' => $bankProfileId,
        ];
    }
}
