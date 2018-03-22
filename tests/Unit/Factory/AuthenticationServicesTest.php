<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory;

use Illuminate\Http\Request;
use StephBug\Firewall\Factory\AuthenticationServices;
use StephBug\Firewall\Factory\Contracts\AuthenticationServiceFactory;
use StephBugTest\Firewall\Mock\SomeAuthenticationRequest;
use StephBugTest\Firewall\Unit\TestCase;

class AuthenticationServicesTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed_with_an_array_of_services(): void
    {
        $one = $this->getMockForAbstractClass(AuthenticationServiceFactory::class);
        $one->expects($this->once())->method('position')->willReturn('form');

        $services = [$one];
        $collection = new AuthenticationServices($services);

        $this->assertEquals($one, $collection->filter('form')->first());
    }

    /**
     * @test
     */
    public function it_can_add_service(): void
    {
        $one = $this->getMockForAbstractClass(AuthenticationServiceFactory::class);
        $one->expects($this->exactly(2))->method('position')->willReturn('form');

        $collection = new AuthenticationServices();
        $collection->add('foo', $one);

        $this->assertEmpty($collection->filter('any'));
        $this->assertEquals($one, $collection->filter('form')->first());
    }

    /**
     * @test
     */
    public function it_can_filter_services_by_position(): void
    {
        $one = $this->getMockForAbstractClass(AuthenticationServiceFactory::class);
        $one->expects($this->once())->method('position')->willReturn('form');

        $two = $this->getMockForAbstractClass(AuthenticationServiceFactory::class);
        $two->expects($this->once())->method('position')->willReturn('pre_auth');

        $services = [$one, $two];

        $collection = new AuthenticationServices($services);

        $this->assertEquals($two, $collection->filter('pre_auth')->first());
    }

    /**
     * @test
     */
    public function it_can_filter_by_request_matcher(): void
    {
        $request = $this->getMockBuilder(Request::class)->getMock();

        $one = $this->getMockForAbstractClass(AuthenticationServiceFactory::class);
        $one->expects($this->any())->method('position')->willReturn('form');
        $one->expects($this->any())->method('matcher')->willReturn(
            new SomeAuthenticationRequest(null, true)
        );

        $two = $this->getMockForAbstractClass(AuthenticationServiceFactory::class);
        $two->expects($this->any())->method('position')->willReturn('form');
        $two->expects($this->any())->method('matcher')->willReturn(
            new SomeAuthenticationRequest(null, false)
        );

        $services = [$one, $two];
        $collection = new AuthenticationServices($services, $request);

        $this->assertCount(1, $collection->filter('form'));
        $this->assertEquals($one, $collection->filter('form')->first());
    }
}