<?php
namespace EasyExpress\Tests\Core;

use Doctrine\Common\Cache\Cache;
use EasyExpress\Core\AccessToken;
use EasyExpress\Core\Http;
use EasyExpress\Tests\TestCase;

class AccessTokenTest extends TestCase
{

    /**
     *
     */
    public function testGetToken()
    {
        $cache = \Mockery::mock(Cache::class, function ($mock) {
            $mock->shouldReceive('fetch')->andReturn('thisIsACachedToken');
            $mock->shouldReceive('save')->andReturnUsing(function ($key, $token, $expire) {
                return $token;
            });
        });

        $http = \Mockery::mock(Http::class.'[json]', function ($mock) {
            $mock->shouldReceive('json')->andReturn(json_encode([
                'body' => [
                    'accessToken' => 'thisIsATokenFromHttp'
                ]
            ]));
        });

        $accessToken = new AccessToken('appIID', 'appKey', 'custId');

        $accessToken->setCache($cache);
        $accessToken->setHttp($http);

        $this->assertEquals('thisIsACachedToken', $accessToken->getToken());

        // forceRefresh
        $this->assertEquals('thisIsATokenFromHttp', $accessToken->getToken(true));
    }



}