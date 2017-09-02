<?php

namespace EasyExpress\Order;

use EasyExpress\Core\AbstractAPI;
use InvalidArgumentException;

/**
 * Class Order
 * @package EasyExpress\Order
 *
 */
class Order extends AbstractAPI
{

    /**
     * 快速下单类型
     */
    const CREATE_ORDER_TYPE = 200;

    /**
     *
     */
    const CREATE_ORDER_URL = "https://open-prod.sf-express.com/rest/v1.0/order/";

    /**
     * 类型
     */
    const QUERY_ORDER_TYPE = 203;

    /**
     *
     */
    const QUERY_ORDER_URL = 'https://open-prod.sf-express.com/rest/v1.0/order/query/';

    /**
     *
     */
    const WAYBILL_IMAGE_TYPE = 205;

    /**
     *
     */
    const WAYBILL_IMAGE_URL = "https://open-prod.sf-express.com/rest/v1.0/waybill/image/";

    /**
     *
     */
    const PRODUCT_ADDITIONAL_QUERY_TYPE = 251;

    /**
     *
     */
    const PRODUCT_ADDITIONAL_QUERY_URL = "https://open-prod.sf-express.com/rest/v1.0/product/additional/query/";

    /**
     * @var array
     */
    public $data = [
        'addedServices' => [],
        'cargoInfo' => [
            "cargo" => "",
            "cargoAmount" => "",
            "cargoCount" => "",
            "cargoTotalWeight" => "",
            "cargoUnit" => "",
            "cargoWeight" => "",
            "parcelQuantity" => ""
        ],
        'consigneeInfo' => [
            "address" => "",
            "city" => "",
            "company" => "",
            "contact" => "",
            "mobile" => "",
            "province" => "",
            "shipperCode" => "",
            "tel" => ""
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
        "remark" => 'dagds',
        "sendStartTime" => ''
    ];

    /**
     * @var array
     */
    protected $required = ['cargoInfo', 'consigneeInfo', 'expressType', 'isDoCall', 'isGenBillNo', 'orderId', 'payMethod'];

    /**
     * @param array $data
     * @return \EasyExpress\Support\Collection
     *
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
     * 
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
     *
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
     *
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