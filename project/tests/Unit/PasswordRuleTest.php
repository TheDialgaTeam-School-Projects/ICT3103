<?php

namespace Tests\Unit;

use App\Rules\Password;
use PHPUnit\Framework\TestCase;

class PasswordRuleTest extends TestCase
{
    /**
     * @var Password
     */
    private $passwordTestRule;

    protected function setUp(): void
    {
        $this->passwordTestRule = new Password();
    }

    public function testPasses()
    {
        $this->assertTrue($this->passwordTestRule->passes(null, 'VCSNfZR93Z#ekzCb'));
    }

    public function testLettersOnly()
    {
        $this->assertFalse($this->passwordTestRule->passes(null, 'fsdfsdfsdfsdf'));
    }

    public function testNumbersOnly()
    {
        $this->assertFalse($this->passwordTestRule->passes(null, '5156156465460'));
    }

    public function testSpecialCharacters()
    {
        $this->assertFalse($this->passwordTestRule->passes(null, '!@#$%^&*()_+'));
    }

    public function testLettersWithCaps()
    {
        $this->assertFalse($this->passwordTestRule->passes(null, 'gagagAfdaafA'));
    }

    public function testLettersAndNumbers()
    {
        $this->assertFalse($this->passwordTestRule->passes(null, 'fdg5s65fg4s6df65'));
    }

    public function testLettersWithCapsAndNumbers()
    {
        $this->assertFalse($this->passwordTestRule->passes(null, 'fdSFDg4sSGdf65'));
    }

    public function testLettersWithCapsAndSpecial()
    {
        $this->assertFalse($this->passwordTestRule->passes(null, 'fdSF%##S&Gdf%'));
    }

    public function testSpecialCharactersAndNumbers()
    {
        $this->assertFalse($this->passwordTestRule->passes(null, '84648^@1651^&165'));
    }
}
