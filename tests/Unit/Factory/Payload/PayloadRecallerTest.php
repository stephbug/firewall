<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Payload;

use StephBug\Firewall\Factory\Payload\PayloadRecaller;
use StephBug\Firewall\Factory\Payload\PayloadService;
use StephBugTest\Firewall\Unit\TestCase;

class PayloadRecallerTest extends TestCase
{
    /**
     * @test
     */
    public function it_access_public_payload_service_property(): void
    {
        $this->assertEquals($this->payloadService, $this->payloadRecallerInstance()->service);
    }

    /**
     * @test
     */
    public function it_access_public_payload_service_key_property(): void
    {
        $this->assertEquals('foo', $this->payloadRecallerInstance()->serviceKey);
    }

    /**
     * @test
     */
    public function it_access_public_payload_firewall_id_property(): void
    {
        $this->assertEquals('bar', $this->payloadRecallerInstance()->firewallId);
    }

    private $payloadService;

    private function payloadRecallerInstance(): PayloadRecaller
    {
        return new PayloadRecaller($this->payloadService, 'foo', 'bar');
    }

    protected function setUp(): void
    {
        $this->payloadService = $this->getMockBuilder(PayloadService::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}