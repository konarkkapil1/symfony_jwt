<?php


namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * @test
     */
    public function testUserName(){
        $user = new User();
        $user->getUsername("konark");
        $this->assertEquals("konark", "konark");
    }
}