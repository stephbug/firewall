<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Authentication\Generic;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Authentication\Generic\IdentifierPasswordRecallerFactory;
use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\Firewall\Factory\Manager\RecallerManager;
use StephBug\Firewall\Factory\Payload\PayloadService;
use StephBug\SecurityModel\Application\Http\Firewall\RecallerAuthenticationFirewall;
use StephBug\SecurityModel\Application\Values\Security\RecallerKey;
use StephBug\SecurityModel\Guard\Authentication\Providers\RecallerAuthenticationProvider;
use StephBug\SecurityModel\Guard\Contract\Guardable;
use StephBug\SecurityModel\Guard\Service\Recaller\Recallable;
use StephBug\SecurityModel\User\UserChecker;
use StephBugTest\Firewall\Mock\SecurityTestKey;
use StephBugTest\Firewall\Unit\TestCase;

class IdentifierPasswordRecallerFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_register_service(): void
    {
        $app = $this->getApplication();
        $manager = $this->getMockBuilder(RecallerManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ins = new IdentifierPasswordRecallerFactory($app, $manager);
        $payload = $this->getMockBuilder(PayloadService::class)->disableOriginalConstructor()->getMock();
        $context = $this->getMockForAbstractClass(FirewallContext::class);

        $recallerKey = new RecallerKey('foo');
        $context->expects($this->once())->method('recaller')->willReturn($recallerKey);

        $manager->expects($this->once())->method('hasService')->willReturn(true);
        $manager->expects($this->once())->method('getService')->willReturn('foo_bar');

        $payload->securityKey = new SecurityTestKey('baz');
        $payload->context = $context;
        $payload->userProviderId = 'some_foo';

        $factory = $ins->create($payload);

        $this->assertEquals(
            $id = 'firewall.' . $ins->serviceKey() . '_firewall.baz',
            $factory->firewall()
        );

        $this->assertEquals(
            $id = 'firewall.' . $ins->serviceKey() . '_provider.baz',
            $factory->provider()
        );
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_when_recaller_key_is_missing_from_context(): void
    {
        $app = $this->getApplication();
        $manager = $this->getMockBuilder(RecallerManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ins = new IdentifierPasswordRecallerFactory($app, $manager);
        $payload = $this->getMockBuilder(PayloadService::class)->disableOriginalConstructor()->getMock();
        $context = $this->getMockForAbstractClass(FirewallContext::class);

        $context->expects($this->once())->method('recaller')->willReturn(null);
        $manager->expects($this->never())->method('hasService');
        $manager->expects($this->never())->method('getService');

        $payload->context = $context;

        $ins->create($payload);
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_when_service_missing_from_recaller_manager(): void
    {
        $app = $this->getApplication();
        $manager = $this->getMockBuilder(RecallerManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ins = new IdentifierPasswordRecallerFactory($app, $manager);
        $payload = $this->getMockBuilder(PayloadService::class)->disableOriginalConstructor()->getMock();
        $context = $this->getMockForAbstractClass(FirewallContext::class);

        $recallerKey = new RecallerKey('baz');
        $context->expects($this->once())->method('recaller')->willReturn($recallerKey);
        $manager->expects($this->once())->method('hasService')->willReturn(false);
        $manager->expects($this->never())->method('getService');

        $payload->context = $context;
        $payload->securityKey = new SecurityTestKey('baz');

        $ins->create($payload);
    }

    /**
     * @test
     */
    public function it_register_service_implementation(): void
    {
        $app = $this->getApplication();
        $manager = $this->getMockBuilder(RecallerManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ins = new IdentifierPasswordRecallerFactory($app, $manager);
        $payload = $this->getMockBuilder(PayloadService::class)->disableOriginalConstructor()->getMock();
        $context = $this->getMockForAbstractClass(FirewallContext::class);

        $recallerKey = new RecallerKey('foo');
        $context->expects($this->once())->method('recaller')->willReturn($recallerKey);

        $manager->expects($this->once())->method('hasService')->willReturn(true);
        $manager->expects($this->once())->method('getService')->willReturn('foo_bar');

        $payload->securityKey = new SecurityTestKey('baz');
        $payload->context = $context;
        $payload->userProviderId = 'some_foo';

        $factory = $ins->create($payload);

        $this->assertEquals(
            $firewallId = 'firewall.' . $ins->serviceKey() . '_firewall.baz',
            $factory->firewall()
        );

        $this->assertEquals(
            $authProviderId = 'firewall.' . $ins->serviceKey() . '_provider.baz',
            $factory->provider()
        );

        //
        $app->instance(Guardable::class, $this->getMockForAbstractClass(Guardable::class));
        $app->instance('foo_bar', $this->getMockForAbstractClass(Recallable::class));

        $app->instance(UserChecker::class, $this->getMockForAbstractClass(UserChecker::class));

        $firewall = $app->make($firewallId);
        $this->assertInstanceOf(RecallerAuthenticationFirewall::class, $firewall);

        $provider = $app->make($authProviderId);
        $this->assertInstanceOf(RecallerAuthenticationProvider::class, $provider);
    }

    /**
     * @test
     */
    public function it_access_service_key(): void
    {
        $ins = $this->getFactoryInstance();

        $this->assertEquals('form-login-recaller', $ins->serviceKey());
    }

    /**
     * @test
     */
    public function it_access_mirror_key(): void
    {
        $ins = $this->getFactoryInstance();

        $this->assertEquals('form-login', $ins->mirrorKey());
    }

    /**
     * @test
     */
    public function it_access_authentication_request(): void
    {
        $ins = $this->getFactoryInstance();

        $this->assertNull($ins->matcher());
    }

    /**
     * @test
     */
    public function it_access_user_provider_key(): void
    {
        $ins = $this->getFactoryInstance();

        $this->assertNull($ins->userProviderKey());
    }

    private function getFactoryInstance(): IdentifierPasswordRecallerFactory
    {
        $app = $this->getApplication();
        $manager = $this->getMockBuilder(RecallerManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new IdentifierPasswordRecallerFactory($app, $manager);
    }

    public function getApplication(): Application
    {
        $app = new \Illuminate\Foundation\Application();
        $app::setInstance(new Container());

        return $app;
    }
}