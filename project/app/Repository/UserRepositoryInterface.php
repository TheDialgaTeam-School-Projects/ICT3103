<?php

namespace App\Repository;

use App\Repository\Eloquent\BankAccountRepositoryInterface;
use App\Repository\Eloquent\BankProfileOtpRepositoryInterface;
use App\Repository\Eloquent\BankProfileRepositoryInterface;
use App\Repository\Eloquent\UserAccountRepositoryInterface;
use App\Repository\Eloquent\UserSessionRepositoryInterface;

interface UserRepositoryInterface extends
    BankProfileRepositoryInterface,
    BankProfileOtpRepositoryInterface,
    BankAccountRepositoryInterface,
    UserAccountRepositoryInterface,
    UserSessionRepositoryInterface
{
}
