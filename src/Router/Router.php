<?php

namespace EasyExpress\Router;


use EasyExpress\Core\AbstractAPI;

class Router extends AbstractAPI
{
    /**
     * 
     */
    const QUERY_ROUTER_TYPE = 501;

    /**
     *
     */
    const QUERY_ROUTER_URL = "https://open-sbox.sf-express.com/rest/v1.0/route/query/";


    /**
     *
     */
    const QUERY_INC_ROUTER_TYPE = 504;

    /**
     *
     */
    const QUERY_INC_ROUTER_URL = "https://open-sbox.sf-express.com/rest/v1.0/route/inc/query/";

    /**
     *
     */
    const WAYBILL_IMAGE_TYPE = 205;

    /**
     *
     */
    const WAYBILL_IMAGE_URL = "https://open-sbox.sf-express.com/rest/v1.0/waybill/image/";

    /**
     *
     */
    const PRODUCT_ADDITIONAL_QUERY_TYPE = 251;

    /**
     *
     */
    const PRODUCT_ADDITIONAL_QUERY_URL = "https://open-sbox.sf-express.com/rest/v1.0/product/additional/query/";

    /**
     * @param $trackingNumber
     * @param $trackingType
     * @return \EasyExpress\Support\Collection
     */
    public function query($trackingNumber, $trackingType)
    {
        $dataHead = [
            "transMessageId" => $this->getTransMessageId(),
            "transType" => self::QUERY_ROUTER_TYPE
        ];

        $data = array(
            "head" => $dataHead,
            "body" => [
                "trackingType" => $trackingType, //2根据订单号查询；1根据运单号查询
                "trackingNumber" => $trackingNumber,
                "methodType" =>  "1"
            ]
        );

        $body = $this->parseJSON('json', [self::QUERY_ROUTER_URL, $data]);

        return $body;
    }

    public function incQuery()
    {
        $dataHead = [
            "transMessageId" => $this->getTransMessageId(),
            "transType" => self::QUERY_INC_ROUTER_TYPE
        ];

        $data = array(
            "head" => $dataHead,
            "body" => [
                "orderId" => "JFKWEBELIEVE10005242131"
            ]
        );

        $body = $this->parseJSON('json', [self::QUERY_INC_ROUTER_URL, $data]);

        return $body;
    }

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
}