<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Mock;

use StephBug\Firewall\Factory\Payload\PayloadRecaller;
use StephBug\Firewall\Factory\Payload\PayloadService;

class SomePayloadRecaller extends PayloadRecaller
{
    public function getService(): PayloadService
    {
        return $this->service;
    }

    public function getServiceKey(): string
    {
        return $this->serviceKey;
    }

    public function firewallId(): string
    {
        return $this->firewallId;
    }
}