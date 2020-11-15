<?php

namespace Tests\Feature;

use App\Http\Controllers\UserRegistrationController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private $seed = true;

    public function testRegisterIdentifyValid()
    {
        $this->refreshTestDatabase();

        $data = [
            'identification_id' => 's9211883F!',
            'date_of_birth' => '1992-05-10',
        ];

        $response = $this->post('/register/identify', $data);
        $response->assertRedirect('/register/verify');
    }

    public function testRegisterIdentifyInvalidIdentification()
    {
        $this->refreshTestDatabase();

        $data = [
            'identification_id' => 'S9211883F',
            'date_of_birth' => '1992-05-10',
        ];

        $response = $this->post('/register/identify', $data);
        $response->assertLocation('/');
    }

    public function testRegisterIdentifyInvalidDateOfBirth()
    {
        $this->refreshTestDatabase();

        $data = [
            'identification_id' => 's9211883F!',
            'date_of_birth' => '1992-10-05',
        ];

        $response = $this->post('/register/identify', $data);
        $response->assertLocation('/');
    }


    public function testRegisterCreate()
    {
        $this->refreshTestDatabase();

        $data = [
            'username' => 'jerry',
            'password' => 's9211883F!',
            'password_confirm' => 's9211883F!',
        ];

        $response = $this->withSession([
            UserRegistrationController::BANK_PROFILE_ID_SESSION_KEY => 4,
            UserRegistrationController::REGISTER_USER_VERIFIED_SESSION_KEY => true,
        ])->post('/register/create', $data);

        $response->assertRedirect('/');
    }
}
