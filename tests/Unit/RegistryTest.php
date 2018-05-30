<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use StephBug\Firewall\Factory;
use StephBug\Firewall\Registry;

class RegistryTest extends TestCase
{
    /**
     * @test
     */
    public function it_register_authentication_services(): void
    {
        $services = new Collection(['foo_bar' => ['test.foo', 'test.bar']]);

        $app = $this->getApplication();

        $factory = $this->getMockBuilder(Factory::class)->disableOriginalConstructor()->getMock();
        $factory->expects($this->once())->method('raise')->willReturn($services);

        $laravelRouter = $this->getMockBuilder(Router::class)->disableOriginalConstructor()->getMock();
        $laravelRouter->expects($this->once())->method('middlewareGroup');

        $registry = new Registry($app, $factory, $laravelRouter);

        $registry->register(new Request(), new Route('GET', 'foo/bar', []));

        $this->assertTrue($app->bound('firewall.middleware.foo_bar'));

        $firewallPipeline = $app->make('firewall.middleware.foo_bar');

        $ref = new \ReflectionClass($firewallPipeline);
        $md = $ref->getProperty('middleware');
        $md->setAccessible(true);

        $this->assertEquals(['test.foo', 'test.bar'], $md->getValue($firewallPipeline));
    }

    private function getApplication(): Application
    {
        $app = new \Illuminate\Foundation\Application();
        $app::setInstance(new Container());

        return $app;
    }
}