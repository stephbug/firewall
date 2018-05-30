<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Bootstrap;

use StephBug\Firewall\Factory\Bootstrap\ImpersonateUser;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBugTest\Firewall\App\HasTestBuilder;
use StephBugTest\Firewall\Unit\TestCase;

class ImpersonateUserTest extends TestCase
{
    use HasTestBuilder;

    /**
     * @test
     */
    public function it_register_impersonate_service(): void
    {
        $app = $this->getApplication();
        $bt = new ImpersonateUser($app);
        $builder = $this->getFirewallBuilder();
        $this->assertEmpty($builder->middleware());

        $this->context->expects($this->once())->method('isAllowedToSwitch')->willReturn(true);
        $key = $this->getMockBuilder(SecurityKey::class)->disableOriginalConstructor()->getMock();
        $key->expects($this->once())->method('value')->willReturn('bar');
        $this->keyContext->expects($this->once())->method('key')->willReturn($key);

        $response = $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));
        $this->assertEquals('foobar', $response);

        $middleware = $builder->middleware();
        $this->assertEquals('firewall.impersonate_user_firewall.bar', array_pop($middleware));
        $this->assertTrue($app->bound('firewall.impersonate_user_firewall.bar'));
    }

    /**
     * @test
     */
    public function it_does_not_register_impersonated_service_if_context_does_not_allow_it(): void
    {
        $app = $this->getApplication();
        $bt = new ImpersonateUser($app);
        $builder = $this->getFirewallBuilder();
        $this->assertEmpty($builder->middleware());

        $this->context->expects($this->once())->method('isAllowedToSwitch')->willReturn(false);

        $response = $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));
        $this->assertEquals('foobar', $response);

        $this->assertEmpty($builder->middleware());
    }
}