<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory;

use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\Firewall\Factory\Builder\UserProviders;
use StephBugTest\Firewall\Unit\TestCase;

class UserProvidersTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed_with_an_array_of_user_providers(): void
    {
        $providers = new UserProviders(['alias' => 'userProviderId']);

        $this->assertInstanceOf(UserProviders::class, $providers);
    }

    /**
     * @test
     */
    public function it_retrieve_user_provider_id_by_alias(): void
    {
        $providers = new UserProviders(['alias' => 'userProviderId']);

        $context = $this->getMockForAbstractClass(FirewallContext::class);

        $this->assertEquals('userProviderId', $providers->get($context, 'alias'));
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_when_alias_does_not_exist(): void
    {
        $providers = new UserProviders(['alias' => 'userProviderId']);

        $context = $this->getMockForAbstractClass(FirewallContext::class);

        $providers->get($context, 'foo');
    }

    /**
     * @test
     */
    public function it_return_user_provider_id_from_context_when_alias_is_not_provided(): void
    {
        $providers = new UserProviders(['alias' => 'userProviderId', 'foo' => 'bar']);

        $context = $this->getMockForAbstractClass(FirewallContext::class);
        $context->expects($this->once())->method('userProviderId')->willReturn('foo');

        $this->assertEquals('bar', $providers->get($context));
    }

    /**
     * @test
     */
    public function it_return_user_providers_ids_as_array(): void
    {
        $providers = new UserProviders(['alias' => 'userProviderId', 'foo' => 'bar']);

        $this->assertEquals(['userProviderId', 'bar'], $providers->toArray());
    }
}