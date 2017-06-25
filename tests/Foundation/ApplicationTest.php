<?php

namespace EasyExpress\Tests\Foundation;


use EasyExpress\Foundation\Application;
use EasyExpress\Tests\TestCase;

class ApplicationTest extends TestCase
{
    public function testAbc()
    {
        $app = new Application([]);

        foreach($app->keys() as $providerName) {

            $this->assertTrue($app[$providerName]->test, '');
        }

    }
}
