<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\App;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Contracts\FirewallContext;

trait HasTestBuilder
{
    protected function getApplication(): Application
    {
        $app = new \Illuminate\Foundation\Application();
        $app::setInstance(new Container());

        return $app;
    }

    protected function getFirewallBuilder(): Builder
    {
        return new Builder(
            $this->map,
            $this->context,
            $this->userProviders,
            $this->keyContext
        );
    }

    protected function getResponseFromLastPipe($response): callable
    {
        return function () use($response) {
            return $response;
        };
    }

    protected $context;
    protected $keyContext;
    protected $map;
    protected $userProviders;

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = $this->getMockForAbstractClass(FirewallContext::class);
        $this->keyContext = $this->getMockBuilder(Builder\SecurityContext::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->map = $this->getMockBuilder(Builder\FirewallMap::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->userProviders = $this->getMockBuilder(Builder\UserProviders::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}