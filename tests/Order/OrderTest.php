<?php

namespace EasyExpress\Tests\Order;


use EasyExpress\Core\Exceptions\HttpException;
use EasyExpress\Core\Exceptions\InvalidArgumentException;
use EasyExpress\Order\Order;
use EasyExpress\Tests\TestCase;

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
            $accessToken = \Mockery::mock('EasyExpress\Core\AccessToken');
            $accessToken->shouldReceive('getToken')->andReturn('foo');
            $accessToken->shouldReceive('getCustId')->andReturn('7550010174');
            $accessToken->shouldReceive('getAppId')->andReturn('00033311');
            $accessToken->shouldReceive('getAppKey')->andReturn('AC9DA1B7452BE5775118CA8DB1237431');

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

        try {
            $order->create();
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
        }

        $data = ['orderId' => 'test'];

        try {
            $order->create($data);
        } catch (\Exception $e) {
            $this->assertInstanceOf(HttpException::class, $e);
        }
    }

    public function testQuery()
    {
        $order = $this->getOrder();

        $orderID = 'test';
        $response = $order->query($orderID);

        $this->assertStringStartsWith(Order::QUERY_ORDER_URL, $response['api']);
        $this->assertEquals($orderID, $response['params']['body']['orderId']);
    }

    /**
     *
     */
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
        $this->assertEquals($cargoInfo, $order->data['cargoInfo']);

        $orderId = 'test_oder_id';
        $remark = 'remark';
        $order->withRemark($remark)->addOrderId($orderId);

        $this->assertEquals($orderId, $order->data['orderId']);
        $this->assertEquals($remark, $order->data['remark']);
    }

}