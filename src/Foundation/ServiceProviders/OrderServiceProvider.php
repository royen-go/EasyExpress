<?php
namespace EasyExpress\Foundation\ServiceProviders;

use EasyExpress\Order\Filter;
use EasyExpress\Order\Notify;
use EasyExpress\Order\Order;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class OrderServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $pimple
     */
    public function register(Container $pimple)
    {
        $pimple['order'] = function ($pimple) {
            return new Order($pimple['access_token']);
        };

        $pimple['filter'] = function ($pimple) {
            return new Filter($pimple['access_token']);
        };

        $pimple['notify'] = function ($pimple) {
            return new Notify($pimple['access_token']);
        };

    }
}