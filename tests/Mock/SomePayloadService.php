<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Mock;

use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\Firewall\Factory\Payload\PayloadService;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;

class SomePayloadService extends PayloadService
{
    public function getSecurityKey(): SecurityKey
    {
        return $this->securityKey;
    }

    public function getFirewallContext(): FirewallContext
    {
        return $this->context;
    }

    public function userProviderId(): string
    {
        return $this->userProviderId;
    }

    public function entrypoint(): ?string
    {
        return $this->entrypoint;
    }
}