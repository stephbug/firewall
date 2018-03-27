<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Payload;

use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBugTest\Firewall\Unit\TestCase;

class PayloadFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_access_firewall(): void
    {
        $this->assertEquals('foo', $this->payloadInstance()->setFirewall('foo')->firewall());
    }

    /**
     * @test
     */
    public function it_access_provider(): void
    {
        $this->assertEquals('foo', $this->payloadInstance()->setProvider('foo')->provider());
    }

    /**
     * @test
     */
    public function it_access_entrypoint(): void
    {
        $this->assertEquals('foo', $this->payloadInstance()->setEntrypoint('foo')->entrypoint());
    }

    private function payloadInstance(): PayloadFactory
    {
        $instance = new PayloadFactory();

        $this->assertNull($instance->firewall());
        $this->assertNull($instance->provider());
        $this->assertNull($instance->entrypoint());

        return $instance;
    }
}