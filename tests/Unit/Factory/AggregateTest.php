<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use StephBug\Firewall\Factory\Aggregate;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBugTest\Firewall\Unit\TestCase;

class AggregateTest extends TestCase
{
    /**
     * @var Aggregate|MockObject
     */
    private $aggregate;

    protected function setUp(): void
    {
        $this->aggregate = new Aggregate();
        $this->assertEmpty($this->aggregate->providers());
        $this->assertEmpty($this->aggregate->firewall());
        $this->assertEmpty($this->aggregate->entrypoints());
    }

    /**
     * @test
     */
    public function it_add_service_using_invokable(): void
    {
        ($this->aggregate)((new PayloadFactory())
            ->setFirewall('firewallId')
            ->setProvider('providerId')
            ->setEntrypoint('entrypointId'));

        $this->assertEquals('firewallId', array_first($this->aggregate->firewall()));
        $this->assertEquals('providerId', array_first($this->aggregate->providers()));
        $this->assertEquals('entrypointId', array_first($this->aggregate->entrypoints()));
    }
}