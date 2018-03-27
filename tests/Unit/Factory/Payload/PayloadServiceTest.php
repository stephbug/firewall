<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Payload;

use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\Firewall\Factory\Payload\PayloadService;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBugTest\Firewall\Unit\TestCase;

class PayloadServiceTest extends TestCase
{
    /**
     * @test
     */
    public function it_access_public_payload_security_key_property(): void
    {
        $this->assertEquals($this->securityKey, $this->payloadServiceInstance()->securityKey);
    }

    /**
     * @test
     */
    public function it_access_public_payload_context_property(): void
    {
        $this->assertEquals($this->context, $this->payloadServiceInstance()->context);
    }

    /**
     * @test
     */
    public function it_access_public_payload_user_provider_property(): void
    {
        $this->assertEquals('foo', $this->payloadServiceInstance()->userProviderId);
    }

    /**
     * @test
     */
    public function it_access_public_payload_entrypoint_property(): void
    {
        $this->assertEquals('bar', $this->payloadServiceInstance()->entrypoint);
    }

    /**
     * @test
     */
    public function it_allow_null_entrypoint_property(): void
    {
        $this->assertNull( $this->payloadServiceInstance(false)->entrypoint);
    }

    private function payloadServiceInstance(bool $entrypoint = true): PayloadService
    {
        return new PayloadService(
            $this->securityKey,
            $this->context,
            'foo',
            $entrypoint ? 'bar' : null
        );
    }

    private $securityKey;
    private $context;

    protected function setUp(): void
    {
        $this->securityKey = $this->getMockBuilder(SecurityKey::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->context = $this->getMockForAbstractClass(FirewallContext::class);
    }
}