<?php

namespace EasyExpress\Foundation\ServiceProviders;

use EasyExpress\Router\Router;
use Pimple\Container;

class RouterServiceProvider
{
    public function register(Container $pimple)
    {
        $pimple['router'] = function () {
            return new Router();
        };
    }

}