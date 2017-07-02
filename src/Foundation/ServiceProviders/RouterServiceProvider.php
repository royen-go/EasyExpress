<?php

namespace EasyExpress\Foundation\ServiceProviders;

use EasyExpress\Router\Router;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RouterServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['router'] = function () {
            return new Router();
        };
    }

}