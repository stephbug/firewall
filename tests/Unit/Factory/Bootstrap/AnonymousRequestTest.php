<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Bootstrap;

use StephBug\Firewall\Factory\Bootstrap\AnonymousRequest;
use StephBug\SecurityModel\Application\Values\Security\AnonymousKey;
use StephBugTest\Firewall\App\HasTestBuilder;
use StephBugTest\Firewall\Unit\TestCase;

class AnonymousRequestTest extends TestCase
{
    use HasTestBuilder;

    /**
     * @test
     */
    public function it_register_anonymous_firewall(): void
    {
        $app = $this->getApplication();
        $bt = new AnonymousRequest($app);
        $key = new AnonymousKey('foo');
        $builder = $this->getFirewallBuilder();

        $this->assertEmpty($builder->middleware());
        $this->assertEmpty($builder->authenticationProviders());

        $this->context->expects($this->once())->method('isAnonymous')->willReturn(true);
        $this->context->expects($this->once())->method('anonymousKey')->willReturn($key);

        $response = $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));
        $this->assertEquals('foobar', $response);

        $serviceIds = $builder->middleware();

        $serviceAlias = array_pop($serviceIds);
        $this->assertTrue($app->bound($serviceAlias));
    }

    /**
     * @test
     */
    public function it_register_anonymous_provider(): void
    {
        $app = $this->getApplication();
        $bt = new AnonymousRequest($app);
        $key = new AnonymousKey('foo');
        $builder = $this->getFirewallBuilder();

        $this->assertEmpty($builder->authenticationProviders());

        $this->context->expects($this->once())->method('isAnonymous')->willReturn(true);
        $this->context->expects($this->once())->method('anonymousKey')->willReturn($key);

        $response = $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));
        $this->assertEquals('foobar', $response);

        $serviceIds = $builder->authenticationProviders();

        $serviceAlias = array_pop($serviceIds);
        $this->assertTrue($app->bound($serviceAlias));
    }

    /**
     * @test
     */
    public function it_does_not_register_anonymous_service_if_anonymous_request_is_not_allowed(): void
    {
        $app = $this->getApplication();
        $bt = new AnonymousRequest($app);
        $builder = $this->getFirewallBuilder();

        $this->assertEmpty($builder->authenticationProviders());
        $this->assertEmpty($builder->middleware());

        $this->context->expects($this->once())->method('isAnonymous')->willReturn(false);
        $this->context->expects($this->never())->method('anonymousKey');

        $response = $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));
        $this->assertEquals('foobar', $response);

        $this->assertEmpty($builder->authenticationProviders());
        $this->assertEmpty($builder->middleware());
    }
}