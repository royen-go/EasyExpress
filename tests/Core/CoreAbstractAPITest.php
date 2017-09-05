<?php
namespace EasyExpress\Tests\Core;

use EasyExpress\Core\AbstractAPI;
use EasyExpress\Core\AccessToken;
use EasyExpress\Core\Exceptions\HttpException;
use EasyExpress\Core\Http;
use EasyExpress\Support\Collection;
use EasyExpress\Tests\TestCase;

class FooAPI extends AbstractAPI
{
    public function getHttpInstance()
    {
        return $this->http;
    }
}

/**
 * Class CoreAbstractAPITest
 * @package EasyExpress\Tests\Core
 *
 */
class CoreAbstractAPITest extends TestCase
{
    /**
     * Test __construct.
     */
    public function testConstruct()
    {
        $accessToken = \Mockery::mock(AccessToken::class);
        $api = new FooAPI($accessToken);
        $this->assertEquals($accessToken, $api->getAccessToken());
    }

    public function testHttpInstance()
    {
        $accessToken = \Mockery::mock(AccessToken::class);
        $api = new FooAPI($accessToken);
        $this->assertNull($api->getHttpInstance());
        $api->getHttp();
        $this->assertInstanceOf(Http::class, $api->getHttpInstance());
        $middlewares = $api->getHttp()->getMiddlewares();
        $this->assertCount(3, $middlewares);
        $http = \Mockery::mock(Http::class.'[getMiddlewares]', function ($mock) {
            $mock->shouldReceive('getMiddlewares')->andReturn([1, 2, 3]);
        });
        $api->setHttp($http);
        $this->assertEquals($http, $api->getHttp());
    }

    public function testParseJSON()
    {
        $accessToken = \Mockery::mock(AccessToken::class);
        $api = new FooAPI($accessToken);
        $http = \Mockery::mock(Http::class.'[getMiddlewares,get,parseJSON]', function ($mock) {
            $mock->shouldReceive('getMiddlewares')->andReturn([1, 2, 3]);
            $mock->shouldReceive('get')->andReturnUsing(function () {
                return func_get_args();
            });
            $mock->shouldReceive('parseJSON')->andReturnUsing(function ($json) {
                return $json;
            });
        });
        $api->setHttp($http);
        $collection = $api->parseJSON('get', ['foo', ['bar']]);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(['foo', ['bar']], $collection->all());
        // test error
        $http = \Mockery::mock(Http::class.'[getMiddlewares,get,parseJSON]', function ($mock) {
            $mock->shouldReceive('getMiddlewares')->andReturn([1, 2, 3]);
            $mock->shouldReceive('get')->andReturnUsing(function () {
                return func_get_args();
            });
            $mock->shouldReceive('parseJSON')->andReturnUsing(function ($json) {
                return ['head' => [
                    'code' => 'EX_CODE_OPENAPI_0500',
                    'message' => ''
                ]];
            });
        });
        $api->setHttp($http);
        $this->setExpectedException(HttpException::class, 'Unknown', 500);
        $collection = $api->parseJSON('get', ['foo', ['bar']]);
        $this->fail();
    }


}