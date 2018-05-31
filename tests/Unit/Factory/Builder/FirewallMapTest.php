<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Builder;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use StephBug\Firewall\Factory\Builder\FirewallMap;
use StephBug\Firewall\Factory\Contracts\AuthenticationServiceFactory;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\Firewall\Factory\Payload\PayloadService;
use StephBugTest\Firewall\Unit\TestCase;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

class FirewallMapTest extends TestCase
{
    /**
     * @test
     */
    public function it_filter_service_registered_as_string(): void
    {
        $map = ['foo_bar'];
        $ins = new FirewallMap($this->getApplication(), $map);

        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();

        $mapped = $ins->matches($request);

        $this->assertEquals(['foo_bar'], $mapped->toArray());
    }

    /**
     * @test
     */
    public function it_filter_service_factory(): void
    {
        $app = $this->getApplication();
        $map = [['foo', 'foo_bar', true]];

        $service = $this->getServiceFactory();
        $app->instance('foo_bar', $service);

        $ins = new FirewallMap($app, $map);

        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $mapped = $ins->matches($request);

        $this->assertEquals($service, $mapped->first());
    }

    /**
     * @test
     */
    public function it_filter_service_factory_by_request_matcher_instance(): void
    {
        $app = $this->getApplication();
        $map = [['foo', 'foo_bar', $this->getRequestMatcher(false)]];

        $service = $this->getServiceFactory();
        $app->instance('foo_bar', $service);

        $ins = new FirewallMap($app, $map);

        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $mapped = $ins->matches($request);

        $this->assertEmpty($mapped->toArray());
    }

    /**
     * @test
     */
    public function it_filter_service_factory_by_request_matcher_bound(): void
    {
        $app = $this->getApplication();
        $map = [['foo', 'foo_bar', 'baz']];

        $service = $this->getServiceFactory();
        $app->instance('foo_bar', $service);

        $app->instance('baz', $this->getRequestMatcher(true));

        $ins = new FirewallMap($app, $map);

        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $mapped = $ins->matches($request);

        $this->assertEquals($service, $mapped->first());
    }

    /**
     * @test
     */
    public function it_does_not_register_service_factory_if_service_key_does_not_match(): void
    {
        $app = $this->getApplication();
        $map = [['foo', 'foo_bar', $this->getRequestMatcher(true)]];

        $service = $this->getInvalidServiceFactory();
        $app->instance('foo_bar', $service);

        $ins = new FirewallMap($app, $map);

        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $mapped = $ins->matches($request);

        $this->assertEmpty($mapped->toArray());
    }

    private function getServiceFactory(): AuthenticationServiceFactory
    {
        return new class() implements AuthenticationServiceFactory
        {
            public function create(PayloadService $payload): PayloadFactory
            {
                return new PayloadFactory();
            }

            public function matcher(): ?RequestMatcherInterface
            {
                return null;
            }

            public function userProviderKey(): ?string
            {
                return null;
            }

            public function serviceKey(): string
            {
                return 'foo';
            }
        };
    }

    private function getInvalidServiceFactory(): AuthenticationServiceFactory
    {
        return new class() implements AuthenticationServiceFactory
        {
            public function create(PayloadService $payload): PayloadFactory
            {
                return new PayloadFactory();
            }

            public function matcher(): ?RequestMatcherInterface
            {
                return null;
            }

            public function userProviderKey(): ?string
            {
                return null;
            }

            public function serviceKey(): string
            {
                return 'BAZ_BAZ';
            }
        };
    }

    private function getRequestMatcher(bool $match): RequestMatcherInterface
    {
        return new class($match) implements RequestMatcherInterface
        {
            /**
             * @var bool
             */
            private $match;

            public function __construct(bool $match)
            {
                $this->match = $match;
            }

            public function matches(\Symfony\Component\HttpFoundation\Request $request)
            {
                return $this->match;
            }
        };
    }

    private function getApplication(): Application
    {
        $app = new \Illuminate\Foundation\Application();
        $app::setInstance(new Container());

        return $app;
    }
}