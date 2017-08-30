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
    const CREATE_ORDER_URL = "https://open-sbox.sf-express.com/rest/v1.0/order/";

    /**
     * 类型
     */
    const QUERY_ORDER_TYPE = 203;

    /**
     *
     */
    const QUERY_ORDER_URL = 'https://open-sbox.sf-express.com/rest/v1.0/order/query/';

    /**
     * 类型
     */
    const FILTER_ORDER_TYPE = 204;

    /**
     *
     */
    const FILTER_ORDER_URL = 'https://open-sbox.sf-express.com/rest/v1.0/order/filter/';

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

    public function filter()
    {
        $dataHead = [
            "transMessageId" => $this->getTransMessageId(),
            "transType" => self::FILTER_ORDER_TYPE
        ];

        $data = array(
            "head" => $dataHead,
            "body" => [
                "filterType" => "1",
                "deliverCustId" => "4342",

                "deliverTel" => "0755-28680875",
                "deliverCountry" => "中国",
                "deliverProvince" =>  "广东省",
                "deliverCity" => "深圳市",
                "deliverCounty" => "福田区",
                "deliverAddress" => "新洲十一街万基商务大厦",

                "consigneeTel" => "13456787123",
                "consigneeCountry" => "中国",
                "consigneeProvince" =>  "广东省",
                "consigneeCity" => "深圳市",
                "consigneeCounty" => "福田区",
                "consigneeAddress" => "新洲十一街万基商务大厦",
            ]
        );

        $body = $this->parseJSON('json', [self::FILTER_ORDER_URL, $data]);
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