<?php

declare(strict_types=1);

namespace StephBug\Firewall;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use StephBug\Firewall\Application\Exception\DebugFirewall;
use StephBug\Firewall\Factory\Factory;
use StephBug\Firewall\Factory\FirewallPipeline;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;

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
        $services = $this->factory->raise(new Collection($route->middleware()), $request);

        $services->each(function (array $middleware, string $name) {
            //$middleware = $this->setExceptionHandler($middleware, $name);

            $pipeline = new FirewallPipeline($name, $middleware, $this->app);

            $this->router->middlewareGroup($name, [$pipeline]);
        });
    }

    protected function setExceptionHandler(array $middleware, string $name): array
    {
        $exception = $this->app->make(array_pop($middleware));

        if (!$exception instanceof DebugFirewall) {
            throw InvalidArgument::reason(
                sprintf('Last middleware of firewall %s must implement %s interface',
                    $name, DebugFirewall::class)
            );
        }

        $this->app->make(ExceptionHandler::class)->setFirewallHandler($exception);

        return $middleware;
    }
}