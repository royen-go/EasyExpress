<?php
namespace EasyExpress\Foundation;

use EasyExpress\Core\AccessToken;
use EasyExpress\Order\Order;
use EasyExpress\Router\Router;
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

        $this->registerProviders();

        $this['access_token'] = function () {
            return new AccessToken();
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

    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }



}
