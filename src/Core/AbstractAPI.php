<?php

namespace EasyExpress\Core;


use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractAPI
{
    /**
     * @var Http
     */
    protected $http;

    protected $middlewares = [];

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
        // retry todo
        $this->http->addMiddleware($this->retryMiddleware());
        // log todo
        $this->http->addMiddleware($this->logMiddleware());
    }

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
                if (stripos($body, 'status') && (stripos($body, '200'))) {

                    return true;
                }
            }

            return false;
        });

    }

    protected function logMiddleware()
    {
        return Middleware::tap(function (RequestInterface $request, $options) {
            var_dump($request->getUri());
        });
    }


}