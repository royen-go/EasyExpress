<?php

namespace EasyExpress\Order;

use EasyExpress\Core\AbstractAPI;
use InvalidArgumentException;

/**
 * Class Filter
 * @package EasyExpress\Order
 *
 */
class Filter extends AbstractAPI
{
    /**
     * 类型
     */
    const FILTER_ORDER_TYPE = 204;

    /**
     *
     */
    const FILTER_ORDER_URL = 'https://open-sbox.sf-express.com/rest/v1.0/filter/';

    /**
     * @var array
     */
    protected $required = ['consigneeCountry', 'consigneeProvince', 'consigneeCity', 'consigneeAddress', 'filterType', 'deliverCustId'];

    /**
     * @var array
     */
    public $data = [
        "filterType" => "1",

        "orderId" => "",
        "deliverTel" => "",
        "deliverCountry" => "",
        "deliverProvince" =>  "",
        "deliverCity" => "",
        "deliverCounty" => "",
        "deliverAddress" => "",
        "deliverCustId" => "4342",

        "consigneeTel" => "",
        "consigneeCountry" => "",
        "consigneeProvince" =>  "",
        "consigneeCity" => "",
        "consigneeCounty" => "",
        "consigneeAddress" => "",
    ];


    /**
     * @param array $data
     * @return \EasyExpress\Support\Collection
     *
     */
    public function query(array $data)
    {
        $dataHead = [
            "transMessageId" => $this->getTransMessageId(),
            "transType" => self::FILTER_ORDER_TYPE
        ];

        $data = array(
            "head" => $dataHead,
            "body" => $this->validParams($data)
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
            'consignee' => 1,
            'orderId' => 'orderId',
            'deliver' => 1
        ];
        if (0 === stripos($method, 'with') && strlen($method) > 4) {
            $method = lcfirst(substr($method, 4));
        }

        if (0 === stripos($method, 'and')) {
            $method = lcfirst(substr($method, 3));
        }

        if (isset($map[$method])) {
            $data = array_shift($args);
            if(is_array($data)) {
                if($method === 'consignee') {
                    foreach($data as $key => $val) {
                        $dataKey = $method . ucfirst($key);
                        $this->data[$dataKey] = $val;
                    }
                }
                if($method === 'deliver') {
                    foreach($data as $key => $val) {
                        $dataKey = $method . ucfirst($key);
                        $this->data[$dataKey] = $val;
                    }
                }
            }

            $this->data[$map[$method]] = $data;
        }

        return $this;
    }


}