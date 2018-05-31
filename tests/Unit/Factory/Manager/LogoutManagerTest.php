<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Manager;

use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use StephBug\Firewall\Factory\Manager\LogoutManager;
use StephBugTest\Firewall\Mock\SecurityTestKey;
use StephBugTest\Firewall\Unit\TestCase;

class LogoutManagerTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_set_logout_context(): void
    {
        $m = new LogoutManager($this->getApplication());

        $key = new SecurityTestKey('foo');

        $context = ['key' => 'service_id'];
        $m->addLogoutContext($key, $context);

        $this->assertTrue($m->hasService($key, 'key'));
    }

    /**
     * @test
     */
    public function it_can_set_multiple_logout_context_for_the_same_security_key(): void
    {
        $m = new LogoutManager($this->getApplication());

        $key = new SecurityTestKey('foo');

        $context = ['baz' => 'bar'];
        $m->addLogoutContext($key, $context);

        $context2 = ['bar' => 'baz'];
        $m->addLogoutContext($key, $context2);

        $this->assertTrue($m->hasService($key, 'baz'));
        $this->assertTrue($m->hasService($key, 'bar'));
    }

    /**
     * @test
     */
    public function it_can_set_multiple_logout_context_for_multiple_security_key(): void
    {
        $m = new LogoutManager($this->getApplication());

        $key = new SecurityTestKey('foo');
        $context = ['baz' => 'bar'];
        $m->addLogoutContext($key, $context);

        $key2 = new SecurityTestKey('foo_bar');
        $context2 = ['bar' => 'baz'];
        $m->addLogoutContext($key2, $context2);

        $this->assertTrue($m->hasService($key, 'baz'));
        $this->assertTrue($m->hasService($key2, 'bar'));
    }

    /**
     * @test
     */
    public function it_add_handler_to_service_key(): void
    {
        $m = new LogoutManager($this->getApplication());

        $key = new SecurityTestKey('foo');

        $context = ['baz' => 'bar'];
        $m->addLogoutContext($key, $context);

        $m = $m->addHandler($key, 'baz', 'foo_bar');

        $this->assertInstanceOf(LogoutManager::class, $m);
        $this->assertTrue($m->hasService($key, 'baz'));
    }

    /**
     * @test
     */
    public function it_can_access_resolved_handlers_by_service_key(): void
    {
        $app = $this->getApplication();

        $app->bind('handler_id', function () {
            return 'foo';
        });

        $key = new SecurityTestKey('foo');

        $m = new LogoutManager($app);
        $context = ['baz' => 'bar'];
        $m->addLogoutContext($key, $context);

        $ins = $m->addHandler($key, 'baz', 'handler_id');

        $handlers = $ins->getResolvedHandlers($key, 'baz');

        $this->assertCount(1, $handlers);
        $this->assertEquals('foo', array_shift($handlers));
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_when_adding_handler_to_an_unknown_service(): void
    {
        $m = new LogoutManager($this->getApplication());

        $key = new SecurityTestKey('foo');
        $this->assertFalse($m->hasService($key, 'bar'));

        $context = ['bar' => 'service_id'];
        $m->addLogoutContext($key, $context);

        $m->addHandler($key, 'foo_bar', 'handler_id');
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     * @dataProvider provideInvalidHandlers
     */
    public function it_raise_exception_when_array_of_handlers_is_empty($handler): void
    {
        $this->expectExceptionMessage(
            sprintf('No logout handler has been registered for service key %s', 'bar')
        );
        $m = new LogoutManager($this->getApplication());

        $key = new SecurityTestKey('foo');
        $this->assertFalse($m->hasService($key, 'bar'));

        $context = ['bar' => $handler];

        $m->addLogoutContext($key, $context);

        $this->assertTrue($m->hasService($key, 'bar'));

        $m->getResolvedHandlers($key, 'bar');
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_when_resolving_handlers_with_unknown_service(): void
    {
        $key = new SecurityTestKey('foo');

        $this->expectExceptionMessage(
            sprintf('No logout service has been registered for service key %s and security key %s',
                'foo_bar','foo')
        );
        $m = new LogoutManager($this->getApplication());

        $this->assertFalse($m->hasService($key, 'bar'));

        $context = ['bar' => 'baz'];

        $m->addLogoutContext($key, $context);

        $this->assertTrue($m->hasService($key, 'bar'));

        $m->getResolvedHandlers($key, 'foo_bar');
    }

    public function provideInvalidHandlers(): array
    {
        return [[[]], ['']];
    }

    private function getApplication()
    {
        $app = new Application();
        $app::setInstance(new Container());

        return $app;
    }
}