<?php

namespace Tests\Feature;

use App\Http\Controllers\UserAuthenticationController;
use App\Models\UserAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    private $seed = true;

    public function testInvalidRecipient()
    {
        $this->refreshTestDatabase();

        $request = $this->actingAs((new UserAccount())->getUserAccount('jianmingyong'))->withSession([UserAuthenticationController::LOGIN_VERIFIED_SESSION_TOKEN => true]);

        $uri = '/dashboard/account/0018527414/transfer';

        $data = [
            'bank_account_id_from' => '0018527414',
            'bank_account_id_to'  => '0000000000',
            'amount'  => '101',
        ];

        $response = $request->post($uri, $data);
        $response->assertRedirect('/');
    }

    public function testInsufficientAmount()
    {
        $this->refreshTestDatabase();

        $request = $this->actingAs((new UserAccount())->getUserAccount('jianmingyong'))->withSession([UserAuthenticationController::LOGIN_VERIFIED_SESSION_TOKEN => true]);

        $uri = '/dashboard/account/0018527414/transfer';
        $data = [
            'bank_account_id_from' => '0018527414',
            'bank_account_id_to' => '0018527413',
            'amount'  => '1101'
        ];

        $response = $request->post($uri, $data);
        $response->assertRedirect($uri);
    }
}
