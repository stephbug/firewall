<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Routing\Strategy;

use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Route;
use StephBug\Firewall\Factory\Routing\Strategy\RouteMatchedStrategy;
use StephBug\Firewall\Registry;
use StephBugTest\Firewall\Unit\TestCase;

class RouteMatchedStrategyTest extends TestCase
{
    /**
     * @test
     */
    public function it_delegate_firewall_handling_on_laravel_route_matched_event(): void
    {
        $strategy = new RouteMatchedStrategy(
            $registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock(),
            $dispatcher = new Dispatcher()
        );

        $request = Request::create('/foo/bar', 'GET');
        $route = new Route('GET', '/foo/bar', ['as' => 'foo.bar']);
        $request->setRouteResolver(function () use ($request, $route) {
            $route->bind($request);
            return $route;
        });

        $event = new RouteMatched($route, $request);
        $registry->expects($this->once())->method('register');

        $strategy->delegateHandling($request);

        $dispatcher->dispatch($event);
    }
}