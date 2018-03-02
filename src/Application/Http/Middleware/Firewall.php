<?php

declare(strict_types=1);

namespace StephBug\Firewall\Application\Http\Middleware;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use StephBug\Firewall\Application\Exception\DebugFirewall;
use StephBug\Firewall\Factory\Contracts\FirewallExceptionRegistry;
use StephBug\Firewall\Factory\Factory;

class Firewall
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    /**
     * @var Application
     */
    private $app;

    public function __construct(Factory $factory, Router $router, Dispatcher $eventDispatcher, Application $app)
    {
        $this->factory = $factory;
        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
        $this->app = $app;
    }

    public function handle(Request $request, \Closure $next)
    {
        $this->eventDispatcher->listen(RouteMatched::class, [$this, 'onEvent']);

        return $next($request);
    }

    protected function processFirewall(Request $request, Route $route): void
    {
        $this->factory
            ->raise(new Collection($route->middleware()), $request)
            ->each(function (array $middleware, string $name) {
                $middleware = $this->setExceptionHandler($middleware, $name);

                $this->router->middlewareGroup($name, $middleware);
            });
    }

    protected function setExceptionHandler(array $middleware, string $name): array
    {
        $exception = $this->app->make(end($middleware));

        if (!$exception instanceof DebugFirewall) {
            throw new \RuntimeException(
                sprintf('Last middleware of firewall %s must implement %s interface',
                    $name, DebugFirewall::class)
            );
        }

        $this->app->make(ExceptionHandler::class)->setFirewallhandler($exception);

        return $middleware;
    }

    public function onEvent(RouteMatched $event): void
    {
        $this->processFirewall($event->request, $event->route);
    }
}