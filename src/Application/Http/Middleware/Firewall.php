<?php

declare(strict_types=1);

namespace StephBug\Firewall\Application\Http\Middleware;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\RouteMatched;
use StephBug\Firewall\ServiceRegistry;

class Firewall
{
    /**
     * @var ServiceRegistry
     */
    private $registry;

    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    public function __construct(ServiceRegistry $registry, Dispatcher $eventDispatcher)
    {
        $this->registry = $registry;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handle(Request $request, \Closure $next)
    {
        $this->eventDispatcher->listen(RouteMatched::class, [$this, 'onEvent']);

        return $next($request);
    }

    public function onEvent(RouteMatched $event): void
    {
        $this->registry->register($event->request, $event->route);
    }
}