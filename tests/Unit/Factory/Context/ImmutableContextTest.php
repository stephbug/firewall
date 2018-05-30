<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Context;

use StephBug\Firewall\Factory\Context\ImmutableContext;
use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\Firewall\Factory\Contracts\MutableContext;
use StephBugTest\Firewall\Unit\TestCase;

class ImmutableContextTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_not_mutate_attribute(): void
    {
        // till class is final, this weak test is ok

        $ins = new ImmutableContext(['foo']);

        $this->assertInstanceOf(FirewallContext::class, $ins);
        $this->assertNotInstanceOf(MutableContext::class, $ins);

        $ref = new \ReflectionClass($ins);
        $this->assertTrue($ref->isFinal());
    }

    /**
     * @test
     */
    public function it_can_access_attributes(): void
    {
        $ins = new ImmutableContext(['foo']);

        $this->assertEquals(['foo'], $ins->getAttributes());
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_with_empty_attributes(): void
    {
        new ImmutableContext([]);
    }
}