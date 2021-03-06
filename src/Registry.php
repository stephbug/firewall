<?php

declare(strict_types=1);

namespace StephBug\Firewall;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use StephBug\Firewall\Factory\Routing\Pipeline\FirewallPipeline;

class Registry
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var Router
     */
    private $router;

    public function __construct(Application $app, Factory $factory, Router $router)
    {
        $this->app = $app;
        $this->factory = $factory;
        $this->router = $router;
    }

    public function register(Request $request, Route $route): void
    {
        $services = $this->factory->raise($route->middleware(), $request);

        $services->each(function (array $middleware, string $name) {
            $firewallId = 'firewall.middleware.' . $name;

            $this->registerFirewall($firewallId, $middleware, $name);

            $this->router->middlewareGroup($name, [$firewallId]);
        });
    }

    protected function registerFirewall(string $firewallId, array $middleware, string $name): void
    {
        $this->app->bind($firewallId, function (Application $app) use ($middleware, $name) {
            return new FirewallPipeline($app, $middleware, $name);
        });
    }
}