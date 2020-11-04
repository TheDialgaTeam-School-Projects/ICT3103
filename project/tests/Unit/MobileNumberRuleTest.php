<?php

namespace Tests\Unit;

use App\Rules\MobileNumber;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class MobileNumberRuleTest extends TestCase
{
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var MobileNumber
     */
    private $mobileNumberRule;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
        $this->mobileNumberRule = new MobileNumber();
    }

    public function testPasses()
    {
        $this->assertTrue($this->mobileNumberRule->passes(null, $this->faker->phoneNumber));
    }

    public function testFails()
    {
        $this->assertFalse($this->mobileNumberRule->passes(null, $this->faker->text));
    }
}
