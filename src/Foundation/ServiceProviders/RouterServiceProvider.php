<?php
namespace EasyExpress\Foundation\ServiceProviders;

use EasyExpress\Router\Router;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RouterServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['router'] = function ($pimple) {
            return new Router($pimple['access_token']);
        };
    }

}