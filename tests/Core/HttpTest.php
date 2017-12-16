<?php
namespace EasyExpress\Tests\Core;

use EasyExpress\Core\Http;
use EasyExpress\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class HttpTest extends TestCase
{
    public function testConstruct()
    {
        $http = new Http();

        $this->assertInstanceOf(Client::class, $http->getClient());
    }

    /**
     *
     */
    public function testChangeBaseUri()
    {
        $http = new Http();
        $http->changeBaseUri('dev');
        $this->assertTrue($http->getBaseUri() === 'https://open-sbox.sf-express.com');
        $http->changeBaseUri('prod');
        $this->assertTrue($http->getBaseUri() === 'https://open-prod.sf-express.com');
    }

    /**
     * Get guzzle mock client.
     *
     * @param null $expected
     *
     * @return Client
     */
    public function getGuzzleWithResponse($expected = null)
    {
        $guzzle = \Mockery::mock(Client::class);
        \Mockery::mock(Response::class.'[getBody]');

        $status = 200;
        $headers = ['X-Foo' => 'Bar'];
        $body = $expected;
        $protocol = '1.1';
        $response = new Response($status, $headers, $body, $protocol);
        $guzzle->shouldReceive('request')->andReturn($response);
        return $guzzle;
    }

    /**
     * Test request() with json response.
     */
    public function testRequestWithJsonResponse()
    {
        $http = new Http();
        $http->setClient($this->getGuzzleWithResponse(json_encode(['errcode' => '0', 'errmsg' => 'ok'])));
        $this->assertEquals(['errcode' => '0', 'errmsg' => 'ok'], json_decode($http->request('http://abc.tel', 'GET')->getBody(), true));

        $http->setClient($this->getGuzzleWithResponse(json_encode(['foo' => 'bar'])));
        $response = $http->request('http://abc.tel', 'GET');
        $this->assertEquals(json_encode(['foo' => 'bar']), $response->getBody());

        $http->setClient($this->getGuzzleWithResponse('non-json content'));
        $response = $http->request('http://abc.tel', 'GET');
        $this->assertEquals('non-json content', $response->getBody());
    }

    /**
     * Test parseJSON().
     */
    public function testParseJSON()
    {
        $http = new Http();
        $http->setClient($this->getGuzzleWithResponse('{"foo:"bar"}'));
        try {
            $http->parseJSON($http->request('http://abc.tel', 'GET'));
            $this->assertFail('Invalid json body check fail.');
        } catch (\Exception $e) {
            $this->assertInstanceOf("\EasyExpress\Core\Exceptions\HttpException", $e);
        }
        $http->setClient($this->getGuzzleWithResponse('{"foo":"bar"}'));
        $this->assertEquals(['foo' => 'bar'], $http->parseJSON($http->request('http://abc.tel', 'GET')));
    }

    /**
     * Test get().
     */
    public function testGet()
    {
        $guzzle = \Mockery::mock(Client::class);
        $http = \Mockery::mock(Http::class.'[request]');
        $http->setClient($guzzle);
        $http->shouldReceive('request')->andReturnUsing(function ($url, $method, $body) {
            return compact('url', 'method', 'body');
        });
        $response = $http->get('http://abc.tel', ['foo' => 'bar']);
        $this->assertEquals('http://abc.tel', $response['url']);
        $this->assertEquals('GET', $response['method']);
        $this->assertEquals(['query' => ['foo' => 'bar']], $response['body']);
    }

    /**
     * Test post().
     */
    public function testPost()
    {
        $guzzle = \Mockery::mock(Client::class);
        $http = \Mockery::mock(Http::class.'[request]');
        $http->setClient($guzzle);
        $http->shouldReceive('request')->andReturnUsing(function ($url, $method, $body) {
            return compact('url', 'method', 'body');
        });
        // array
        $response = $http->post('http://abc.tel', ['foo' => 'bar']);
        $this->assertEquals('http://abc.tel', $response['url']);
        $this->assertEquals('POST', $response['method']);
        $this->assertEquals(['form_params' => ['foo' => 'bar']], $response['body']);
        // string
        $response = $http->post('http://abc.tel', 'hello here.');
        $this->assertEquals('http://abc.tel', $response['url']);
        $this->assertEquals('POST', $response['method']);
        $this->assertEquals(['body' => 'hello here.'], $response['body']);
    }

    /**
     * Test json().
     */
    public function testJson()
    {
        $guzzle = \Mockery::mock(Client::class);
        $http = \Mockery::mock(Http::class.'[request]');
        $http->setClient($guzzle);
        $http->shouldReceive('request')->andReturnUsing(function ($url, $method, $body) {
            return compact('url', 'method', 'body');
        });

        $response = $http->json('http://abc.tel', ['foo' => 'bar']);
        $this->assertEquals('http://abc.tel', $response['url']);
        $this->assertEquals('POST', $response['method']);
        $this->assertEquals([], $response['body']['query']);
        $this->assertEquals(json_encode(['foo' => 'bar']), $response['body']['body']);
        $this->assertEquals(['content-type' => 'application/json'], $response['body']['headers']);

        $response = $http->json('http://abc.tel', ['foo' => 'bar'], JSON_UNESCAPED_UNICODE);
        $this->assertEquals('http://abc.tel', $response['url']);
        $this->assertEquals('POST', $response['method']);
        $this->assertEquals([], $response['body']['query']);
        $this->assertEquals(json_encode(['foo' => 'bar']), $response['body']['body']);
        $this->assertEquals(['content-type' => 'application/json'], $response['body']['headers']);
    }


}
