<?php
namespace EasyExpress\Tests\Foundation;


use EasyExpress\Foundation\Application;
use EasyExpress\Foundation\Config;
use EasyExpress\Tests\TestCase;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ApplicationTest extends TestCase
{
    /**
     * Test __constructor().
     */
    public function testConstructor()
    {
        $app = new Application(['foo' => 'bar']);

        $this->assertInstanceOf(Config::class, $app['config']);

        $providers = $app->getProviders();
        foreach ($providers as $provider) {
            $container = new Container();
            $container->register(new $provider());
            $container['config'] = $app->raw('config');
            $container['access_token'] = $app->raw('access_token');
            $container['cache'] = $app->raw('cache');
            foreach ($container->keys() as $providerName) {
                $this->assertEquals($container->raw($providerName), $app->raw($providerName));
            }
            unset($container);
        }

    }

    /**
     * Test addProvider() and setProviders.
     */
    public function testProviders()
    {
        $app = new Application(['foo' => 'bar']);
        $providers = $app->getProviders();
        $app->addProvider(\Mockery::mock(ServiceProviderInterface::class));

        $this->assertCount(count($providers) + 1, $app->getProviders());
        $app->setProviders(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $app->getProviders());
    }

    /**
     * test __set, __get.
     */
    public function testMagicMethod()
    {
        $app = new Application(['foo' => 'bar']);
        $app->foo = 'bar';
        // getter setter
        $this->assertEquals('bar', $app->foo);
    }

}
