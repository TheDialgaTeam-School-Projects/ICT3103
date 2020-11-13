<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private $seed = true;

    /**
     * Test login feature .
     *
     * @return void
     */
    public function testLogin()
    {
        $this->refreshTestDatabase();

        $this->assertDatabaseHas('user_account', [
            'username' => 'najib'
        ]);

        $data = [
            'username' => 'najib',
            'password' => 's9501315E!',
        ];

        $response = $this->post('/', $data);
        $response->assertRedirect('/login/2fa');
    }

    /**
     * Test login feature .
     *
     * @return void
     */
    public function testLoginFailed()
    {
        $this->refreshTestDatabase();

        $this->assertDatabaseHas('user_account', [
            'username' => 'najib'
        ]);

        $data = [
            'username' => 'najib',
            'password' => 'sodnfosDNfdj@!',
        ];

        $response = $this->post('/', $data);
        $response->assertLocation('/');
    }
}
