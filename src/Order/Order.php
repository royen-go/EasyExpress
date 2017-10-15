<?php

namespace EasyExpress\Order;

use EasyExpress\Core\AbstractAPI;
use EasyExpress\Core\Exceptions\InvalidArgumentException;

/**
 * Class Order
 * @package EasyExpress\Order
 *
 * @method Order withOrderId($value)
 * @method Order withRemark($value)
 * @method Order withCargo($value)
 * @method Order withConsignee($value)
 * @method Order withDeliver($value)
 * @method Order withPayMethod($value)
 *
 */
class Order extends AbstractAPI
{

    /**
     * 快速下单
     */
    const CREATE_ORDER_TYPE = 200;

    /**
     *
     */
    const CREATE_ORDER_URL = "/rest/v1.0/order/";

    /**
     * 订单查询
     */
    const QUERY_ORDER_TYPE = 203;

    /**
     *
     */
    const QUERY_ORDER_URL = '/rest/v1.0/order/query/';

    /**
     *
     */
    const WAYBILL_IMAGE_TYPE = 205;

    /**
     *
     */
    const WAYBILL_IMAGE_URL = "/rest/v1.0/waybill/image/";

    /**
     *
     */
    const PRODUCT_ADDITIONAL_QUERY_TYPE = 251;

    /**
     *
     */
    const PRODUCT_ADDITIONAL_QUERY_URL = "/rest/v1.0/product/additional/query/";

    /**
     * @var array
     */
    public $data = [
        'addedServices' => [],
        'cargoInfo' => [
            "cargo" => "", //must
            "cargoAmount" => "",
            "cargoCount" => "",
            "cargoTotalWeight" => "",
            "cargoUnit" => "",
            "cargoWeight" => "",
            "parcelQuantity" => ""
        ],
        'consigneeInfo' => [
            "address" => "",// must
            "city" => "",//must
            "company" => "",//must
            "contact" => "",//must
            "mobile" => "",
            "province" => "",//must
            "shipperCode" => "",
            "tel" => ""//must
        ],
        'deliverInfo' => [
            "address" => "",
            "city" => "",
            "company" => "",
            "contact" => "",
            "country" => "",
            "mobile" => "",
            "province" => "",
            "shipperCode" => "",
            "tel" => ""
        ],
        "custId" => '',
        "expressType" => 1,
        "isDoCall" => 1,
        "isGenBillNo" => 1,
        "isGenEletricPic" => 1,
        "needReturnTrackingNo" => 0,
        "orderId" => '',
        "payArea" => "",
        "payMethod" => 1,
        "remark" => '',
        "sendStartTime" => ''
    ];

    /**
     * @var array
     */
    protected $required = ['orderId', 'expressType', 'payMethod', 'custId', 'cargoInfo', 'consigneeInfo'];

    /**
     * @param array $data
     * @return \EasyExpress\Support\Collection
     * @throws InvalidArgumentException
     * @throws \EasyExpress\Core\Exceptions\HttpException
     */
    public function create(array $data = [])
    {
        $addedServices = [];
        $this->data['addedServices'] = $addedServices;

        $this->data['sendStartTime'] = date('Y-m-d H:i:s');

        $this->data['custId'] = $this->accessToken->getCustId();

        $dataHead = [
            "transMessageId" => $this->getTransMessageId(),
            "transType" => self::CREATE_ORDER_TYPE
        ];

        $data = array(
            "head" => $dataHead,
            "body" => $this->validParams($data)
        );
        $body = $this->parseJSON('json', [self::CREATE_ORDER_URL, $data]);
        return $body;
    }

    /**
     * @param $orderID
     * @return \EasyExpress\Support\Collection
     * @throws \EasyExpress\Core\Exceptions\HttpException
     */
    public function query($orderID)
    {
        $dataHead = [
            "transMessageId" => $this->getTransMessageId(),
            "transType" => self::QUERY_ORDER_TYPE
        ];

        $data = array(
            "head" => $dataHead,
            "body" => ['orderId' => $orderID]
        );

        $body = $this->parseJSON('json', [self::QUERY_ORDER_URL, $data]);
        return $body;
    }

    /**
     * @param $orderId
     * @return \EasyExpress\Support\Collection
     * @throws \EasyExpress\Core\Exceptions\HttpException
     */
    public function waybillDownload($orderId)
    {
        $dataHead = [
            "transMessageId" => $this->getTransMessageId(),
            "transType" => self::WAYBILL_IMAGE_TYPE
        ];

        $data = array(
            "head" => $dataHead,
            "body" => [
                "orderId" => $orderId
            ]
        );

        $body = $this->parseJSON('json', [self::WAYBILL_IMAGE_URL, $data]);
        return $body;
    }

    /**
     * @return \EasyExpress\Support\Collection
     * @throws \EasyExpress\Core\Exceptions\HttpException
     */
    public function queryProductAdditional()
    {
        $dataHead = [
            "transMessageId" => $this->getTransMessageId(),
            "transType" => self::PRODUCT_ADDITIONAL_QUERY_TYPE
        ];

        $data = array(
            "head" => $dataHead,
            "body" => new \stdClass()
        );

        $body = $this->parseJSON('json', [self::PRODUCT_ADDITIONAL_QUERY_URL, $data]);
        return $body;
    }

    /**
     * @param array $data
     * @return array
     * @throws InvalidArgumentException
     */
    protected function validParams(array $data = [])
    {
        $params = array_merge($this->data, $data);

        foreach ($params as $key => $value) {
            if (in_array($key, $this->required, true) && empty($value) && empty($this->data[$key])) {
                throw new InvalidArgumentException("Attribute '$key' can not be empty!");
            }
            $params[$key] = empty($value) ? $this->data[$key] : $value;
        }

        return $params;
    }

    /**
     * @param $method
     * @param $args
     * @return $this
     *
     */
    public function __call($method, $args)
    {
        $map = [
            "cargo" => 'cargoInfo',
            "consignee" => 'consigneeInfo',
            "deliver" => 'deliverInfo',
            "orderId" => 'orderId',
            "payMethod" => 'payMethod',
            "remark" => 'remark'
        ];

        if (0 === stripos($method, 'with') && strlen($method) > 4) {
            $method = lcfirst(substr($method, 4));
        }

        if (0 === stripos($method, 'and')) {
            $method = lcfirst(substr($method, 3));
        }

        if (isset($map[$method])) {
            $this->data[$map[$method]] = array_shift($args);
        }

        return $this;
    }

}