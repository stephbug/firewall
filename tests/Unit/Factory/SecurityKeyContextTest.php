<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory;

use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\Firewall\Factory\Builder\SecurityKeyContext;
use StephBugTest\Firewall\Mock\SecurityTestKey;
use StephBugTest\Firewall\Unit\TestCase;

class SecurityKeyContextTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_access_security_key(): void
    {
        $sec = new SecurityKeyContext($key = new SecurityTestKey('foo'));

        $this->assertEquals($key, $sec->originalKey());
    }

    /**
     * @test
     */
    public function it_return_same_key_when_key_from_context_is_equals(): void
    {
        $sec = new SecurityKeyContext($key = new SecurityTestKey('foo'));

        $context = $this->getMockForAbstractClass(FirewallContext::class);
        $context->expects($this->once())->method('securityKey')->willReturn($key);

        $this->assertEquals($key, $sec->key($context));
    }

    /**
     * @test
     */
    public function it_return_same_key_when_firewall_context_is_stateless(): void
    {
        $sec = new SecurityKeyContext($key = new SecurityTestKey('foo'));
        $diffKey = new SecurityTestKey('bar');

        $context = $this->getMockForAbstractClass(FirewallContext::class);
        $context->expects($this->once())->method('securityKey')->willReturn($diffKey);
        $context->expects($this->once())->method('isStateless')->willReturn(true);

        $this->assertEquals($key, $sec->key($context));
    }

    /**
     * @test
     */
    public function it_can_compare_security_key_from_context(): void
    {
        $sec = new SecurityKeyContext($key = new SecurityTestKey('foo'));
        $context = $this->getMockForAbstractClass(FirewallContext::class);
        $context->expects($this->any())->method('securityKey')->willReturn($key);
        $this->assertTrue($sec->hasSameContext($context));

        $diffKey = new SecurityTestKey('bar');
        $context2 = $this->getMockForAbstractClass(FirewallContext::class);
        $context2->expects($this->any())->method('securityKey')->willReturn($diffKey);

        $this->assertFalse($sec->hasSameContext($context2));
    }

    /**
     * @test
     */
    public function it_can_serialize_security_key(): void
    {
        $sec = new SecurityKeyContext($key = new SecurityTestKey('foo'));

        $context = $this->getMockForAbstractClass(FirewallContext::class);
        $context->expects($this->once())->method('securityKey')->willReturn($key);

        $this->assertEquals('foo', $sec->toString($context));
    }
}