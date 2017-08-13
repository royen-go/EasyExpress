<?php

namespace EasyExpress\Order;

use EasyExpress\Core\AbstractAPI;
use EasyExpress\Core\AccessToken;
use InvalidArgumentException;

/**
 * Class Order
 * @package EasyExpress\Order
 *
 */
class Order extends AbstractAPI
{
    /**
     *
     */
    const CREATE_ORDER_URL = "https://open-sbox.sf-express.com/rest/v1.0/order/";

    /**
     * @var
     */
    private $custID;

    /**
     * @var array
     */
    public $dataHead = [];

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
     * Order constructor.
     * @param AccessToken $accessToken
     */
    public function __construct(AccessToken $accessToken)
    {
        parent::__construct($accessToken);

        $this->custID = $accessToken->getCustId();

        $this->dataHead = [
            "transMessageId" => date('YmdHis', time()).mt_rand(1000, 9999),
            "transType" => 200
        ];

        $addedServices = [];
        $this->data['addedServices'] = $addedServices;

        $this->data['sendStartTime'] = date('Y-m-d H:i:s');

        $this->data['custId'] = $this->custID;
    }


    /**
     * @param array $data
     * @return \EasyExpress\Support\Collection
     *
     */
    public function create(array $data = [])
    {
        $data = array(
            "head" => $this->dataHead,
            "body" => $this->validParams($data)
        );
        $body = $this->parseJSON('json', [self::CREATE_ORDER_URL, $data]);
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