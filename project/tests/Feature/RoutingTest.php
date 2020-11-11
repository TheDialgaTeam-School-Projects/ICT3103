<?php

namespace Tests\Feature;

use App\Models\UserAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutingTest extends TestCase
{
    use RefreshDatabase;

    private $seed = true;

    /**
     * A HomePage Test .
     *
     * @return void
     */
    public function testHomePageTest()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * An authorized access test .
     *
     * @return void
     */
    public function testAuthorizedAccess()
    {
        $this->refreshTestDatabase();

        $request = $this->actingAs((new UserAccount())->getUserAccount('jianmingyong'));
        $response = $request->get('/dashboard/account/list');
        $response->assertRedirect('/login/2fa');
    }

    /**
     * An unauthorized access test .
     *
     * @return void
     */
    public function testUnauthorizedAccess()
    {
        $response = $this->get('/dashboard/account/list');
        $response->assertStatus(302);
    }
}
