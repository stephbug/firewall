<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Context;

use StephBug\Firewall\Factory\Context\DefaultContext;
use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\Firewall\Factory\Contracts\MutableContext;
use StephBug\SecurityModel\Application\Values\Security\AnonymousKey;
use StephBug\SecurityModel\Application\Values\Security\RecallerKey;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBugTest\Firewall\Unit\TestCase;

class DefaultContextTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed_with_empty_array(): void
    {
        $context = new DefaultContext();

        $this->assertInstanceOf(FirewallContext::class, $context);

        $this->assertNotInstanceOf(MutableContext::class, $context);
    }

    /**
     * @test
     */
    public function it_can_be_constructed_with_values(): void
    {
        $context = new DefaultContext(['anonymous' => true]);

        $this->assertTrue($context->isAnonymous());
    }

    /**
     * @test
     */
    public function it_assert_is_anonymous(): void
    {
        $context = new DefaultContext($this->getAttributes());

        $this->assertFalse($context->isAnonymous());
    }

    /**
     * @test
     */
    public function it_assert_is_stateless(): void
    {
        $context = new DefaultContext($this->getAttributes());

        $this->assertFalse($context->isStateless());
    }

    /**
     * @test
     */
    public function it_access_security_key(): void
    {
        $context = new DefaultContext($this->getAttributes());

        $this->assertInstanceOf(SecurityKey::class, $context->securityKey());

        $this->assertEquals('default_security_key', $context->securityKey()->value());
    }

    /**
     * @test
     */
    public function it_access_recaller_key(): void
    {
        $context = new DefaultContext($this->getAttributes());

        $this->assertInstanceOf(RecallerKey::class, $context->recaller('service_key'));

        $this->assertEquals('recaller_key', $context->recaller('service_key')->value());
    }

    /**
     * @test
     */
    public function it_return_null_recaller_payload_if_service_key_does_not_exists(): void
    {
        $context = new DefaultContext($this->getAttributes());

        $this->assertNull($context->recaller('foo_bar'));
    }

    /**
     * @test
     */
    public function it_access_logout_payload_by_service_key(): void
    {
        $context = new DefaultContext($this->getAttributes());

        $this->assertEquals(['payload'], $context->logoutByKey('service_key'));
    }

    /**
     * @test
     */
    public function it_return_null_logout_payload_if_service_key_does_not_exists(): void
    {
        $attributes = array_merge($this->getAttributes(), ['logout' => []]);

        $context = new DefaultContext($attributes);

        $this->assertNull($context->logoutByKey('foo_bar'));
        $this->assertNull($context->logoutByKey('service_key'));
    }

    /**
     * @test
     */
    public function it_assert_logout_service_key_exists(): void
    {
        $context = new DefaultContext($this->getAttributes());

        $this->assertTrue($context->hasLogoutKey('service_key'));
    }

    /**
     * @test
     */
    public function it_access_logout_payload(): void
    {
        $context = new DefaultContext($this->getAttributes());

        $this->assertEquals(['service_key' => ['payload']], $context->logout());
    }

    /**
     * @test
     */
    public function it_access_anonymous_key_if_context_is_anonymous(): void
    {
        $attributes = array_merge($this->getAttributes(), ['anonymous' => true]);

        $context = new DefaultContext($attributes);

        $this->assertInstanceOf(AnonymousKey::class, $context->anonymousKey());
        $this->assertEquals('default_anonymous_key', $context->anonymousKey()->value());
    }

    /**
     * @test
     */
    public function it_assert_allow_to_switch(): void
    {
        $context = new DefaultContext($this->getAttributes());

        $this->assertTrue($context->isAllowedToSwitch());
    }

    /**
     * @test
     */
    public function it_return_null_anonymous_key_if_context_is_not_anonymous(): void
    {
        $context = new DefaultContext($this->getAttributes());

        $this->assertNull($context->anonymousKey());
    }

    /**
     * @test
     */
    public function it_access_user_provider_id(): void
    {
        $context = new DefaultContext($this->getAttributes());

        $this->assertEquals('eloquent', $context->userProviderId());
    }

    /**
     * @test
     */
    public function it_access_entrypoint_id(): void
    {
        $context = new DefaultContext($this->getAttributes());

        $this->assertEquals('default_entry_point_id', $context->entrypointId());
    }

    /**
     * @test
     */
    public function it_access_denied_handler_id(): void
    {
        $context = new DefaultContext($this->getAttributes());

        $this->assertEquals('default_unauthorized_id', $context->unauthorizedId());
    }

    private function getAttributes(): array
    {
        return [
            'anonymous' => false,
            'stateless' => false,
            'allowToSwitch' => true,
            'securityKey' => 'default_security_key',
            'recallerKey' => 'default_recaller_key',
            'anonymousKey' => 'default_anonymous_key',
            'userProviderId' => 'eloquent',
            'entrypointId' => 'default_entry_point_id',
            'unauthorizedId' => 'default_unauthorized_id',
            'logout' => [
                'service_key' => ['payload']
            ],
            'recaller' => [
                'service_key' => 'recaller_key'
            ],
        ];
    }
}