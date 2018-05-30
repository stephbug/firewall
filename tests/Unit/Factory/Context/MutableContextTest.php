<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Context;

use StephBug\Firewall\Factory\Context\MutableContext;
use StephBugTest\Firewall\Unit\TestCase;

class MutableContextTest extends TestCase
{
    /**
     * @test
     */
    public function it_set_any_existing_attribute(): void
    {
        $ins = new MutableContext($this->getAttributes());

        $this->assertFalse($ins->isStateless());

        $ins->setAttribute('stateless', true);

        $this->assertTrue($ins->isStateless());
    }

    /**
     * @test
     */
    public function it_can_be_transform_to_an_immutable_context(): void
    {
        $ins = new MutableContext($this->getAttributes());

        $this->assertEquals($ins->getAttributes(), $ins->toImmutable());
        $this->assertEquals($this->getAttributes(), $ins->toImmutable());
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_if_attribute_does_not_exist(): void
    {
        $ins = new MutableContext($this->getAttributes());

        $ins->setAttribute('foo', 'bar');
    }

    /**
     * @test
     */
    public function it_can_set_anonymous(): void
    {
        $ins = new MutableContext($this->getAttributes());
        $this->assertFalse($ins->isAnonymous());
        $ins->setAnonymous(true);
        $this->assertTrue($ins->isAnonymous());
    }

    /**
     * @test
     */
    public function it_can_set_anonymous_key(): void
    {
        $ins = new MutableContext($this->getAttributes());
        $ins->setAnonymous(true);

        $this->assertEquals('default_anonymous_key', $ins->anonymousKey()->value());

        $ins->setAnonymousKey('foo');
        $this->assertEquals('foo', $ins->anonymousKey()->value());
    }

    /**
     * @test
     */
    public function it_can_set_stateless(): void
    {
        $ins = new MutableContext($this->getAttributes());
        $this->assertFalse($ins->isStateless());
        $ins->setStateless(true);
        $this->assertTrue($ins->isStateless());
    }

    /**
     * @test
     */
    public function it_can_set_entrypoint_id(): void
    {
        $ins = new MutableContext($this->getAttributes());

        $this->assertEquals('default_entry_point_id', $ins->entrypointId());
        $ins->setEntrypointId('foo');
        $this->assertEquals('foo', $ins->entrypointId());
    }

    /**
     * @test
     */
    public function it_can_set_denied_handler_id(): void
    {
        $ins = new MutableContext($this->getAttributes());

        $this->assertEquals('default_unauthorized_id', $ins->unauthorizedId());
        $ins->setUnauthorizedId('foo');
        $this->assertEquals('foo', $ins->unauthorizedId());
    }

    /**
     * @test
     */
    public function it_can_add_logout_payload(): void
    {
        $ins = new MutableContext($this->getAttributes());

        $this->assertEmpty($ins->logout());

        $ins->addLogout('foo', ['bar']);

        $this->assertEquals(['foo' => ['bar']], $ins->logout());
        $this->assertEquals(['bar'], $ins->logoutByKey('foo'));
    }

    /**
     * @test
     */
    public function it_can_add_recaller_payload(): void
    {
        $ins = new MutableContext($this->getAttributes());

        $this->assertNull($ins->recaller('foo'));

        $ins->addRecaller('foo', 'bar');

        $this->assertEquals('bar', $ins->recaller('foo')->value());
    }

    /**
     * @test
     */
    public function it_can_allow_to_switch(): void
    {
        $ins = new MutableContext($this->getAttributes());

        $this->assertTrue($ins->isAllowedToSwitch());

        $ins->allowToSwitch(false);

        $this->assertFalse($ins->isAllowedToSwitch());
    }

    private function getAttributes(): array
    {
        return [
            'anonymous' => false,
            'stateless' => false,
            'securityKey' => 'default_security_key',
            'anonymousKey' => 'default_anonymous_key',
            'userProviderId' => 'eloquent',
            'entrypointId' => 'default_entry_point_id',
            'unauthorizedId' => 'default_unauthorized_id',
            'logout' => [],
            'recaller' => [],
            'allowToSwitch' => true,
        ];
    }
}