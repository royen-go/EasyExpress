<?php

namespace EasyExpress\Router;


use EasyExpress\Core\AbstractAPI;

class Router extends AbstractAPI
{

    /**
     * 2根据订单号查询
     */
    const TRACKING_TYPE_ORDER = 2;

    /**
     * 1根据运单号查询
     */
    const TRACKING_TYPE_WAYBILL = 1;

    /**
     * 
     */
    const QUERY_ROUTER_TYPE = 501;

    /**
     *
     */
    const QUERY_ROUTER_URL = "/rest/v1.0/route/query/";


    /**
     *
     */
    const QUERY_INC_ROUTER_TYPE = 504;

    /**
     *
     */
    const QUERY_INC_ROUTER_URL = "/rest/v1.0/route/inc/query/";

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
                "trackingType" => $trackingType,
                "trackingNumber" => $trackingNumber,
                "methodType" =>  "1" //标准查询【默认值】
            ]
        );

        $body = $this->parseJSON('json', [$this->accessToken->getHostUrl() . self::QUERY_ROUTER_URL, $data]);

        return $body;
    }

    /**
     * @param $orderId
     * @return \EasyExpress\Support\Collection
     */
    public function incQuery($orderId)
    {
        $dataHead = [
            "transMessageId" => $this->getTransMessageId(),
            "transType" => self::QUERY_INC_ROUTER_TYPE
        ];

        $data = array(
            "head" => $dataHead,
            "body" => [
                "orderId" => $orderId
            ]
        );

        $body = $this->parseJSON('json', [$this->accessToken->getHostUrl() . self::QUERY_INC_ROUTER_URL, $data]);

        return $body;
    }


}