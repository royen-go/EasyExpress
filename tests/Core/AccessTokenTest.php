<?php

namespace EasyExpress\Tests\Core;


use EasyExpress\Core\AccessToken;
use EasyExpress\Tests\TestCase;

class AccessTokenTest extends TestCase
{

    public function testGetToken()
    {
        $accessToken = new AccessToken();

        $token = $accessToken->getToken();

        $this->assertNotEmpty($token);
    }

}