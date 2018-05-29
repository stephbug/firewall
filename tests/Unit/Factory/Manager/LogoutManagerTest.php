<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Manager;

use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use StephBug\Firewall\Factory\Manager\LogoutManager;
use StephBugTest\Firewall\Unit\TestCase;

class LogoutManagerTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_set_logout_context(): void
    {
        $m = new LogoutManager($this->getApplication());

        $context = ['key' => 'service_id'];
        $m->setLogoutContext($context);

        $this->assertTrue($m->hasService('key'));
    }

    /**
     * @test
     */
    public function it_add_handler_to_service_key(): void
    {
        $m = new LogoutManager($this->getApplication());
        $context = ['key' => 'service_id'];
        $m->setLogoutContext($context);

       $ins = $m->addHandler('handler_id', 'key');

       $this->assertInstanceOf(LogoutManager::class, $ins);
       $this->assertTrue($m->hasService('key'));
    }

    /**
     * @test
     */
    public function it_can_access_resolved_handlers_by_service_key(): void
    {
        $app = $this->getApplication();

        $app->bind('handler_id',function(){
            return 'foo';
        });

        $m = new LogoutManager($app);
        $context = ['service_key' => 'service_id'];
        $m->setLogoutContext($context);

        $ins = $m->addHandler('handler_id', 'service_key');

        $handlers = $ins->getResolvedHandlers('service_key');

        $this->assertCount(1, $handlers);
        $this->assertEquals('foo', array_shift($handlers));
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_when_adding_handler_to_an_unknown_service_key(): void
    {
        $m = new LogoutManager($this->getApplication());
        $this->assertFalse($m->hasService('bar'));

        $context = ['bar' => 'service_id'];
        $m->setLogoutContext($context);

        $m->addHandler('handler_id', 'foo');
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_when_array_of_handlers_is_empty(): void
    {
        $m = new LogoutManager($this->getApplication());
        $this->assertFalse($m->hasService('bar'));

        $context = ['bar' => 'service_id'];
        $m->setLogoutContext($context);

        $this->assertTrue($m->hasService('bar'));

        $m->getResolvedHandlers('bar');
    }

    private function getApplication()
    {
        $app = new Application();
        $app::setInstance(new Container());

        return $app;
    }
}