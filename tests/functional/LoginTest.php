<?php


namespace App\Tests\functional;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    public function testUserLogin(){
        $client = self::createClient();
        $client->request("POST", "/auth/login", [
            "email" => "konarkkapil2@gmail.com",
            "password" => "konark"
        ]);
        echo($client->getResponse());
        $this->assertTrue($client->getResponse()->isSuccessful());
    }


}