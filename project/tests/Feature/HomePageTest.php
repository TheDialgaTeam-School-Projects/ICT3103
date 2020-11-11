<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

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
}
