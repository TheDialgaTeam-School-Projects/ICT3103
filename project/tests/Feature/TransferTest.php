<?php

namespace Tests\Feature;

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

        $request = $this->actingAs((new UserAccount())->getUserAccount('jianmingyong'));

        $uri = '/dashboard/account/0018527414/transfer';

        $data = [
            'bank_account_id_from' => '0018527414',
            'bank_account_id_to'  => '0000000000',
            'amount'  => '100',
        ];

        $response = $request->post($uri, $data);
        $response->assertRedirect('/');
    }

    public function testInsufficientAmount()
    {
        $this->refreshTestDatabase();

        $request = $this->actingAs((new UserAccount())->getUserAccount('jianmingyong'));

        $uri = '/dashboard/account/0018527414/transfer';
        $data = [
            'bank_account_id_from' => '0018527414',
            'bank_account_id_to' => '0018527413',
            'amount'  => '1001'
        ];

        $response = $request->post($uri, $data);
        $response->assertRedirect($uri);
    }
}
