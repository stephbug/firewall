<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Bootstrap;

use StephBug\Firewall\Factory\Bootstrap\LogoutService;
use StephBug\Firewall\Factory\Manager\LogoutManager;
use StephBugTest\Firewall\App\HasTestBuilder;
use StephBugTest\Firewall\Unit\TestCase;

class LogoutServiceTest extends TestCase
{
    use HasTestBuilder;

    /**
     * @test
     */
    public function it_register_logout_service(): void
    {
        $manager = $this->getMockBuilder(LogoutManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $manager->expects($this->once())->method('setLogoutContext');

        $bt = new LogoutService($this->getApplication(), $manager);
        $builder = $this->getFirewallBuilder();

        $this->context->expects($this->once())->method('logout')->willReturn(['foo']);

        $response = $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));
        $this->assertEquals('foobar', $response);
    }

    /**
     * @test
     */
    public function it_does_not_register_logout_service_when_logout_payload_is_empty(): void
    {
        $manager = $this->getMockBuilder(LogoutManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $manager->expects($this->never())->method('setLogoutContext');

        $bt = new LogoutService($this->getApplication(), $manager);
        $builder = $this->getFirewallBuilder();

        $this->context->expects($this->once())->method('logout')->willReturn([]);

        $response = $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));
        $this->assertEquals('foobar', $response);
    }
}