<?php

namespace EasyExpress\Core;


use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;

class AccessToken
{
    /**
     *
     */
    const API_TOKEN_GET = 'https://open-sbox.sf-express.com/public/v1.0/security/access_token/sf_appid/%s/sf_appkey/%s';

    private $appId;
    private $appKey;
    private $custId;

    /**
     * @var Http
     */
    protected $http;

    /**
     * Cache
     *
     * @var Cache
     */
    protected $cache;

    protected $cacheKey;

    protected $prefix = 'easyexpress.access_token.';

    /**
     * AccessToken constructor.
     * @param $appID
     * @param $appKey
     * @param $custId
     */
    public function __construct($appID, $appKey, $custId)
    {
        $this->appId = $appID;
        $this->appKey = $appKey;
        $this->custId = $custId;
    }

    /**
     * @return mixed
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @return mixed
     */
    public function getAppKey()
    {
        return $this->appKey;
    }

    /**
     * @return mixed
     * @author renshuai
     */
    public function getCustId()
    {
        return $this->custId;
    }

    /**
     * Return the http instance.
     *
     * @return \EasyExpress\Core\Http
     */
    public function getHttp()
    {
        return $this->http ?: $this->http = new Http();
    }

    public function setHttp($http)
    {
        $this->http = $http;
    }

    public function getCacheKey()
    {
        if (is_null($this->cacheKey)) {
            return $this->prefix.$this->appId;
        }

        return $this->cacheKey;
    }

    public function getCache()
    {
        return $this->cache ?: $this->cache = new FilesystemCache(sys_get_temp_dir());
    }

    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @param bool $forceRefresh
     * @return false|string
     * @author renshuai
     */
    public function getToken($forceRefresh = false)
    {
        $cacheKey = $this->getCacheKey();
        $cached = $this->getCache()->fetch($cacheKey);

        if ($forceRefresh || empty($cached)) {
            $token = $this->getTokenFromServer();

            $accessToken = $token['body']['accessToken'];
            $this->getCache()->save($cacheKey, $accessToken, 3400);

            return $accessToken;
        }

        return $cached;
    }

    public function getTokenFromServer()
    {
        $params = [
            "head" => [
                "transMessageId" => date('Ymd', time()) . mt_rand(1000000000, 9999999999),
                "transType" => 301
            ],
            "body" => null
        ];

        $http = $this->getHttp();

        $url = sprintf(self::API_TOKEN_GET, $this->appId, $this->appKey);

        $token = $http->parseJSON($http->json($url, $params));

        return $token;
    }
}