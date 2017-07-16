<?php

namespace EasyExpress\Core;


use EasyExpress\Core\Exceptions\HttpException;
use EasyExpress\Support\Collection;
use EasyExpress\Support\Log;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractAPI
{
    private $appId;

    private $appKey;

    /**
     * @var AccessToken
     */
    protected $accessToken;

    /**
     * @var Http
     */
    protected $http;

    protected $middlewares = [];

    /**
     * Constructor.
     *
     * @param AccessToken $accessToken
     */
    public function __construct(AccessToken $accessToken)
    {
        $this->setAccessToken($accessToken);
    }

    /**
     * @param $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getHttp()
    {
        if (is_null($this->http)) {
            $this->http = new Http();
        }

        if (count($this->http->getMiddlewares()) === 0) {
            $this->registerHttpMiddlewares();
        }

        return $this->http;
    }

    protected function registerHttpMiddlewares()
    {
        // log
        $this->http->addMiddleware($this->logMiddleware());
        // retry
        $this->http->addMiddleware($this->retryMiddleware());
        // access token
        $this->http->addMiddleware($this->accessTokenMiddleware());
    }

    /**
     * Attache access token to request query.
     *
     * @return \Closure
     */
    protected function accessTokenMiddleware()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if (!$this->accessToken) {
                    return $handler($request, $options);
                }
                $token = $this->accessToken->getToken();

                $uri = $request->getUri();
                $path = $uri->getPath();
                $path .= 'access_token/' . $token . '/sf_appid/' . $this->accessToken->getAppId() .  '/sf_appkey/' . $this->accessToken->getAppKey();
                $uri = $uri->withPath($path);
                $request = $request->withUri($uri);

                Log::debug("attache access token : {$uri}");
                Log::debug("attache path : {$path}");
                return $handler($request, $options);
            };
        };
    }

    /**
     * @return callable
     */
    protected function retryMiddleware()
    {
        return Middleware::retry(function (
            $retries,
            RequestInterface $request,
            ResponseInterface $response = null
        ) {
            // Limit the number of retries to 2
            if ($retries <= 1 && $response && $body = $response->getBody()) {
                // Retry on server errors
                if (stripos($body, 'code') && (stripos($body, 'EX_CODE_OPENAPI_0103') || stripos($body, 'EX_CODE_OPENAPI_0105'))) {
                    $token = $this->accessToken->getToken(true);

                    $uri = $request->getUri();
                    $path = $uri->getPath();
                    $path .= 'access_token/' . $token . '/sf_appid/' . $this->appId .  '/sf_appkey/' . $this->appKey;
                    $newUri = $uri->withPath($path);
                    $request->withUri($newUri);

                    Log::debug("Retry with Request Token: {$token}");
                    Log::debug("Retry with Request Uri: {$newUri}");

                    return true;
                }
            }

            return false;
        });

    }

    /**
     * @return callable
     */
    protected function logMiddleware()
    {
        return Middleware::tap(function (RequestInterface $request, $options) {
            Log::debug("Request: {$request->getMethod()} {$request->getUri()} ".json_encode($options));
            Log::debug('Request headers:'.json_encode($request->getHeaders()));
        });
    }

    /**
     * @param $method
     * @param array $args
     * @return Collection
     * @throws Exceptions\HttpException
     */
    public function parseJSON($method, array $args)
    {
        $http = $this->getHttp();

        $contents = $http->parseJSON(call_user_func_array([$http, $method], $args));

        $this->checkAndThrow($contents);

        return new Collection($contents);
    }

    /**
     * @param array $contents
     * @throws HttpException
     *
     */
    protected function checkAndThrow(array $contents)
    {
        if (isset($contents['head']) && isset($contents['head']['code']) && 'EX_CODE_OPENAPI_0200' !== $contents['head']['code']) {

            if (empty($contents['head']['message'])) {
                $contents['head'] = 'Unknown';
            }
            throw new HttpException($contents['head']['message'], $contents['head']['code']);
        }
    }

}