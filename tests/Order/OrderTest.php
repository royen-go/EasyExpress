<?php

namespace EasyExpress\Tests\Order;


use EasyExpress\Core\AccessToken;
use EasyExpress\Core\Exceptions\HttpException;
use EasyExpress\Order\Order;
use EasyExpress\Tests\TestCase;
use InvalidArgumentException;

class OrderTest extends TestCase
{

    /**
     * @param bool $mockHttp
     *
     * @return Order
     *
     */
    public function getOrder($mockHttp = false)
    {
        if ($mockHttp) {
            $accessToken = new AccessToken('00033311', 'AC9DA1B7452BE5775118CA8DB1237431', '7550010174');
//            $accessToken = \Mockery::mock('EasyExpress\Core\AccessToken');
//            $accessToken->shouldReceive('getToken')->andReturn('foo');
//            $accessToken->shouldReceive('getCustId')->andReturn('7550010174');
//            $accessToken->shouldReceive('getAppId')->andReturn('00033311');
//            $accessToken->shouldReceive('getAppKey')->andReturn('AC9DA1B7452BE5775118CA8DB1237431');

            $order = new Order($accessToken);
            $http = \Mockery::mock('EasyExpress\Core\Http[json]');
            $http->shouldReceive('json')->andReturnUsing(function ($api, $params) {
                return json_encode(compact('api', 'params'));
            });
            $order->setHttp($http);
            return $order;
        }

        $accessToken = \Mockery::mock('EasyExpress\Core\AccessToken');
        $accessToken->shouldReceive('getCustId')->andReturn('cust_id');

        $order = \Mockery::mock('\EasyExpress\Order\Order[parseJSON]', [$accessToken]);

        $order->shouldReceive('parseJSON')->andReturnUsing(function ($api, $params) {
            if (isset($params[1])) {
                return ['api' => $params[0], 'params' => $params[1]];
            }
            return ['api' => $params[0]];
        });

        return $order;
    }

    public function testCreate()
    {
        $order = $this->getOrder(true);

        try{
            $order->create();
        }catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
        }

        $data = [
            'orderId' => 'test'
        ];

        try{
            $order->create($data);
        }catch (\Exception $e) {
            $this->assertInstanceOf(HttpException::class, $e);
        }

        $data = [
            'orderId' => 'JFKWEBELIEVE10005242129'
        ];
        $cargoInfo = [
            "cargo" => '手机',
            "cargoAmount" => "",
            "cargoCount" => "",
            "cargoIndex" => 0,
            "cargoTotalWeight" => "",
            "cargoUnit" => "",
            "cargoWeight" => "",
            "orderId"=>"",
            "parcelQuantity" => 1
        ];

        $order->withCargo($cargoInfo);

        $consignessInfo = [
            "address" => "这里",
            "city" => "广州",
            "company" => "小鱼",
            "contact" => "小鱼",
            "mobile" => "17821143272",
            "province" => "广东",
            "shipperCode" => "",
            "tel" => "17821143272"
        ];

        $order->withConsignee($consignessInfo);

        $result = $order->create($data);
        $this->assertEquals('EX_CODE_OPENAPI_0200', $result['head']['code']);
        $this->assertEquals('4200', $result['head']['transType']);

    }

    public function testFilter()
    {
        $order = $this->getOrder(true);

        $result = $order->filter();

        $this->assertEquals('EX_CODE_OPENAPI_0200', $result['head']['code']);
        $this->assertEquals('4204', $result['head']['transType']);
    }


    public function testMagicAccess()
    {
        $accessToken = \Mockery::mock('EasyExpress\Core\AccessToken');
        $accessToken->shouldReceive('getCustId')->andReturn('cust_id_for_test');

        $order = new Order($accessToken);

        $order->withDeliver(new \stdClass());

        $cargoInfo = [
            "cargo" => '233',
            "cargoAmount" => "444",
            "cargoCount" => "rgsdgasd",
            "cargoTotalWeight" => "gasdgasd",
            "cargoUnit" => "gsadg",
            "cargoWeight" => "gasdgsag",
            "parcelQuantity" => 1
        ];

        $order->withCargo($cargoInfo);

        $this->assertEquals($cargoInfo['cargo'], $order->data['cargoInfo']['cargo']);
    }

}