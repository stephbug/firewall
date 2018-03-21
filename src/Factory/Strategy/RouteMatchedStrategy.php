<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Strategy;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\RouteMatched;
use StephBug\Firewall\ServiceRegistry;

class RouteMatchedStrategy implements FirewallStrategy
{
    /**
     * @var ServiceRegistry
     */
    private $registry;

    /**
     * @var Dispatcher
     */
    private $events;

    public function __construct(ServiceRegistry $registry, Dispatcher $events)
    {
        $this->registry = $registry;
        $this->events = $events;
    }

    public function delegateHandling(Request $request): void
    {
        $this->events->listen(RouteMatched::class, [$this, 'onEvent']);
    }

    public function onEvent(RouteMatched $event): void
    {
        $this->registry->register($event->request, $event->route);
    }
}