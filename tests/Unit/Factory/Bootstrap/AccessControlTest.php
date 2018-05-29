<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Bootstrap;

use StephBug\Firewall\Factory\Bootstrap\AccessControl;
use StephBugTest\Firewall\App\HasTestBuilder;
use StephBugTest\Firewall\Unit\TestCase;

class AccessControlTest extends TestCase
{
    use HasTestBuilder;

    /**
     * @test
     */
    public function it_register_access_control(): void
    {
        $app = $this->getApplication();
        $bt = new AccessControl($app);
        $builder = $this->getFirewallBuilder();

        $this->assertEmpty($builder->middleware());

        $response = $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));
        $this->assertEquals('foobar', $response);

        $this->assertTrue($app->bound(AccessControl::SERVICE_ALIAS));

        $serviceIds = $builder->middleware();
        $this->assertEquals(AccessControl::SERVICE_ALIAS, array_pop($serviceIds));
    }
}