<?php

namespace EasyExpress\Core;


use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;

class AccessToken
{
    const API_TOKEN_GET = 'https://open-sbox.sf-express.com/public/v1.0/security/access_token/sf_appid/%s/sf_appkey/%s';

    private $appId = '00029291';
    private $appKey = '345ABEF53B6F4A75463A3D625F9763BB';


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

    protected $prefix = 'easyexpress.shunfeng.access_token.';


    public function __construct()
    {

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
                "transMessageId" => date('Ymd', time()) . mt_rand(1000000000, 9999999999), //todo 这里不行，随机数也会重复
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