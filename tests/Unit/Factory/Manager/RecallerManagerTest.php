<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Manager;

use Illuminate\Container\Container;
use Illuminate\Contracts\Cookie\QueueingFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Cookie\CookieJar;
use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\Firewall\Factory\Manager\RecallerManager;
use StephBug\SecurityModel\Application\Values\Security\RecallerKey;
use StephBug\SecurityModel\Guard\Service\Recaller\Providers\RecallerProvider;
use StephBug\SecurityModel\Guard\Service\Recaller\Recallable;
use StephBugTest\Firewall\Mock\SecurityTestKey;
use StephBugTest\Firewall\Mock\SomePayloadRecaller;
use StephBugTest\Firewall\Mock\SomePayloadService;
use StephBugTest\Firewall\Unit\TestCase;

class RecallerManagerTest extends TestCase
{
    /**
     * @test
     */
    public function it_register_callback_service(): void
    {
        $m = new RecallerManager($this->getApplication());

        $ref = new \ReflectionClass($m);
        $cb = $ref->getProperty('callback');
        $cb->setAccessible(true);
        $this->assertEmpty($cb->getValue($m));

        $m->extend('foo', 'foo_bar', function () {
            return 'baz';
        });

        $val = $cb->getValue($m)['foo']['foo_bar'];

        $this->assertEquals('baz', value($val));
    }

    /**
     * @test
     */
    public function it_register_service_with_default_callback_service(): void
    {
        $app = $this->getApplication();
        $m = new RecallerManager($app);
        $key = new SecurityTestKey('foo');

        $ref = new \ReflectionClass($m);
        $cb = $ref->getProperty('callback');
        $cb->setAccessible(true);
        $this->assertEmpty($cb->getValue($m));

        // setup payload
        $this->payloadRecaller->serviceKey = 'foobar';
        $this->payloadRecaller->firewallId = 'baz_baz';
        $this->payloadService->securityKey = $key;

        $m->make($this->payloadRecaller);

        $this->assertTrue($m->hasService($key, 'foobar'));
        $this->assertEquals('firewall.simple_recaller_service.baz_baz', $m->getService($key, 'foobar'));
        $this->assertTrue($app->bound('firewall.simple_recaller_service.baz_baz'));
    }

    /**
     * @test
     */
    public function it_return_same_service_if_is_already_registered(): void
    {
        $app = $this->getApplication();
        $m = new RecallerManager($app);
        $key = new SecurityTestKey('foo');

        // setup payload
        $this->payloadRecaller->serviceKey = 'foobar';
        $this->payloadRecaller->firewallId = 'baz_baz';
        $this->payloadService->securityKey = $key;

        $m->make($this->payloadRecaller);

        $this->assertTrue($m->hasService($key, 'foobar'));
        $this->assertEquals('firewall.simple_recaller_service.baz_baz', $m->getService($key, 'foobar'));
        $this->assertTrue($app->bound('firewall.simple_recaller_service.baz_baz'));

        $service = $m->getService($key, 'foobar');
        $service2 = $m->make($this->payloadRecaller);

        $this->assertEquals($service, $service2);
    }

    /**
     * @test
     */
    public function it_register_service_with_custom_callback_service(): void
    {
        $app = $this->getApplication();
        $m = new RecallerManager($app);
        $key = new SecurityTestKey('foo');

        $ref = new \ReflectionClass($m);
        $cb = $ref->getProperty('callback');
        $cb->setAccessible(true);
        $this->assertEmpty($cb->getValue($m));

        // setup payload
        $this->payloadRecaller->serviceKey = 'foobar';
        $this->payloadRecaller->firewallId = 'baz_baz';
        $this->payloadService->securityKey = $key;

        $m->extend('foo', 'foobar', function () {
            return 'firewall.custom_callback';
        });

        $m->make($this->payloadRecaller);

        $this->assertTrue($m->hasService($key, 'foobar'));
        $this->assertEquals('firewall.custom_callback', $m->getService($key, 'foobar'));
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_accessing_service_with_invalid_key(): void
    {
        $this->expectExceptionMessage(sprintf(
                'No recaller service has been registered for service key %s and security key %s',
                'biz', 'foo'
            )
        );

        $app = $this->getApplication();
        $m = new RecallerManager($app);
        $key = new SecurityTestKey('foo');

        // setup payload
        $this->payloadRecaller->serviceKey = 'foobar';
        $this->payloadRecaller->firewallId = 'baz_baz';
        $this->payloadService->securityKey = $key;

        $m->make($this->payloadRecaller);

        $this->assertFalse($m->hasService($key, 'biz'));
        $m->getService($key, 'biz');
    }

    /**
     * @test
     */
    public function it_set_callback_service_on_resolving_firewall(): void
    {
        $app = $this->getApplication();
        $m = new RecallerManager($app);
        $key = new SecurityTestKey('foo');

        $ref = new \ReflectionClass($m);
        $cb = $ref->getProperty('callback');
        $cb->setAccessible(true);
        $this->assertEmpty($cb->getValue($m));

        // setup payload
        $this->payloadRecaller->serviceKey = 'foobar';
        $this->payloadRecaller->firewallId = 'baz_baz';
        $this->payloadService->securityKey = $key;
        $this->payloadService->userProviderId = 'provider';

        $m->make($this->payloadRecaller);

        $this->assertTrue($m->hasService($key, 'foobar'));
        $this->assertEquals('firewall.simple_recaller_service.baz_baz', $m->getService($key, 'foobar'));
        $this->assertTrue($app->bound('firewall.simple_recaller_service.baz_baz'));

        $recallerKey = new RecallerKey('secret');
        $this->context->expects($this->once())->method('recaller')->willReturn($recallerKey);

        // bind firewall id and dependency
        $app->bind('baz_baz', function () {
            return new class()
            {
                private $recaller;

                public function setRecaller(Recallable $recaller)
                {
                    $this->recaller = $recaller;
                }

                public function getRecaller(): ?Recallable
                {
                    return $this->recaller;
                }
            };
        });
        $app->bind(QueueingFactory::class, function () {
            return new CookieJar();
        });
        $app->bind('provider', function () {
            return $this->getMockForAbstractClass(RecallerProvider::class);
        });

        $firewall = $app->make('baz_baz');

        $this->assertInstanceOf(Recallable::class, $firewall->getRecaller());
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_if_setter_method_missing_on_resolving_firewall(): void
    {
        $app = $this->getApplication();
        $m = new RecallerManager($app);
        $key = new SecurityTestKey('foo');

        // setup payload
        $this->payloadRecaller->serviceKey = 'foobar';
        $this->payloadRecaller->firewallId = 'baz_baz';
        $this->payloadService->securityKey = $key;
        $this->payloadService->userProviderId = 'provider';

        $m->make($this->payloadRecaller);

        $this->assertTrue($m->hasService($key, 'foobar'));
        $this->assertEquals('firewall.simple_recaller_service.baz_baz', $m->getService($key, 'foobar'));
        $this->assertTrue($app->bound('firewall.simple_recaller_service.baz_baz'));

        $app->bind('baz_baz', function () {
            return new class()
            {
            };
        });

        $app->make('baz_baz');
    }

    private function getApplication(): Application
    {
        $app = new \Illuminate\Foundation\Application();
        $app::setInstance(new Container());

        return $app;
    }

    private $payloadRecaller;
    private $payloadService;
    private $context;

    protected function setUp(): void
    {
        $this->payloadRecaller = $this->getMockBuilder(SomePayloadRecaller::class)->disableOriginalConstructor()->getMock();
        $this->payloadService = $this->getMockBuilder(SomePayloadService::class)->disableOriginalConstructor()->getMock();
        $this->context = $this->getMockForAbstractClass(FirewallContext::class);

        $this->payloadRecaller->service = $this->payloadService;
        $this->payloadService->context = $this->context;
    }
}