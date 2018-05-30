<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Context\DefaultContext;
use StephBug\Firewall\Factory\Context\ImmutableContext;
use StephBug\Firewall\Factory\Context\MutableContext;
use StephBug\Firewall\Manager;

class ManagerTest extends TestCase
{
    /**
     * @test
     */
    public function it_resolve_firewall(): void
    {
        $app = $this->getApplication();
        $this->registerConfigRepository($app);
        $ins = new Manager($app);

        $this->assertInstanceOf(Builder::class, $ins->guard('firewall_name'));
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_when_firewall_name_does_not_exist(): void
    {
        $app = $this->getApplication();
        $this->registerConfigRepository($app);
        $ins = new Manager($app);
        $ins->guard('foo');
    }

    /**
     * @test
     */
    public function it_assert_firewall_name_exists(): void
    {
        $app = $this->getApplication();
        $this->registerConfigRepository($app);
        $ins = new Manager($app);

        $this->assertTrue($ins->hasFirewall('firewall_name'));
        $this->assertFalse($ins->hasFirewall('foo_bar'));
    }

    /**
     * @test
     */
    public function it_resolve_firewall_context(): void
    {
        $app = $this->getApplication();
        $this->registerConfigRepository($app);
        $ins = new Manager($app);

        $builder = $ins->guard('firewall_name');
        $this->assertInstanceOf(DefaultContext::class, $builder->context());
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_if_firewall_context_is_missing(): void
    {
        $this->expectExceptionMessage(
            sprintf('Firewall context missing for firewall name %', 'firewall_name')
        );

        $config = [
            'services' => [
                'firewall_name' => [
                    'context' => '',
                    'map' => ['foo', 'foo_bar', true],
                ],
            ],
            'user_providers' => ['baz' => 'bar_baz'],
        ];

        $app = $this->getApplication();
        $this->registerConfigRepository($app, $config);
        $ins = new Manager($app);
        $ins->guard('firewall_name');
    }

    /**
     * @test
     */
    public function it_turn_a_context_to_an_immutable_context(): void
    {
        $config = [
            'services' => [
                'firewall_name' => [
                    'context' => MutableContext::class,
                    'map' => ['foo', 'foo_bar', true],
                ],
            ],
            'user_providers' => ['baz' => 'bar_baz'],
        ];

        $app = $this->getApplication();
        $this->registerConfigRepository($app, $config);
        $ins = new Manager($app);

        $builder = $ins->guard('firewall_name');

        $this->assertNotInstanceOf(MutableContext::class, $builder->context());
        $this->assertInstanceOf(ImmutableContext::class, $builder->context());
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_if_firewall_map_is_empty(): void
    {
        $this->expectExceptionMessage(
            sprintf('Register at least one service in map configuration %', 'firewall_name')
        );

        $config = [
            'services' => [
                'firewall_name' => [
                    'context' => MutableContext::class,
                    'map' => '',
                ],
            ],
            'user_providers' => ['baz' => 'bar_baz'],
        ];

        $app = $this->getApplication();
        $this->registerConfigRepository($app, $config);
        $ins = new Manager($app);

        $ins->guard('firewall_name');
    }

    private function getApplication(): Application
    {
        $app = new \Illuminate\Foundation\Application();
        $app::setInstance(new Container());

        return $app;
    }

    private function registerConfigRepository(Application $app, array $config = null)
    {
        $items = $config ?? $this->getConfig();

        $config = new Repository(['firewall' => $items]);

        $app->instance(Repository::class, $config);
        $app->alias(Repository::class, 'config');
    }

    private function getConfig(): array
    {
        return [
            'services' => [
                'firewall_name' => [
                    'context' => DefaultContext::class,
                    'map' => ['foo', 'foo_bar', true],
                ],
            ],
            'user_providers' => ['baz' => 'bar_baz'],
        ];
    }
}