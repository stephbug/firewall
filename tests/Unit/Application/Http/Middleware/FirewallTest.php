<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Application\Http\Middleware;

use Illuminate\Http\Request;
use StephBug\Firewall\Application\Http\Middleware\Firewall;
use StephBug\Firewall\Factory\Routing\Strategy\FirewallStrategy;
use StephBugTest\Firewall\Unit\TestCase;

class FirewallTest extends TestCase
{
    /**
     * @test
     */
    public function it_delegate_authentication_process_to_a_strategy(): void
    {
        $m = $this->getMockForAbstractClass(FirewallStrategy::class);
        $m->expects($this->once())->method('delegateHandling');

        $middleware = new Firewall($m);

        $middleware->handle(new Request(), function(){});
    }
}