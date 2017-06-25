<?php
namespace EasyExpress\Tests\Core;

use EasyExpress\Core\Http;
use EasyExpress\Tests\TestCase;
use GuzzleHttp\Client;

class HttpTest extends TestCase
{
    public function testConstruct()
    {
        $http = new Http();

        $this->assertInstanceOf(Client::class, $http->getClient());
    }

}
