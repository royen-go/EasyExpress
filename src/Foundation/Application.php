<?php
namespace EasyExpress\Foundation;

use EasyExpress\Core\AccessToken;
use EasyExpress\Order\Order;
use EasyExpress\Router\Router;
use EasyExpress\Support\Log;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;

/**
 * Class Application
 * @package EasyExpress\Foundation
 *
 *
 * @property Order $order
 * @property Router $router
 *
 */
class Application extends Container
{
    /**
     * @var array
     */
    protected $providers = [
        ServiceProviders\OrderServiceProvider::class,
        ServiceProviders\RouterServiceProvider::class,
    ];

    /**
     * Application constructor.
     *
     * @param array $config
     *
     */
    public function __construct($config)
    {
        parent::__construct();

        $this['config'] = function() use ($config) {
            return new Config($config);
        };

        $this->registerBase();

        $this->registerProviders();

        $this->initializeLogger();

    }

    private function initializeLogger()
    {
        if (Log::hasLogger()) {
            return;
        }

        $logger = new Logger('express');

        if (!$this['config']['debug'] || defined('PHPUNIT_RUNNING')) {
            $logger->pushHandler(new NullHandler());
        } elseif ($this['config']['log.handler'] instanceof HandlerInterface) {
            $logger->pushHandler($this['config']['log.handler']);
        } elseif ($logFile = $this['config']['log.file']) {
            $logger->pushHandler(new StreamHandler(
                    $logFile,
                    $this['config']->get('log.level', Logger::WARNING),
                    true,
                    $this['config']->get('log.permission', null))
            );
        }

        Log::setLogger($logger);
    }
    /**
     *
     */
    private function registerBase()
    {
        $this['access_token'] = function () {
            return new AccessToken(
                $this['config']['appID'],
                $this['config']['appKey'],
                $this['config']['custID']
            );
        };

    }

    /**
     * Return all providers.
     *
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }

    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     *
     */
    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }



}
