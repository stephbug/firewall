<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Payload;

class PayloadRecaller
{

    /**
     * @var PayloadService
     */
    public $service;

    /**
     * @var string
     */
    public $serviceKey;

    /**
     * @var string
     */
    public $firewallId;

    public function __construct(PayloadService $service, string $serviceKey, string $firewallId)
    {
        $this->service = $service;
        $this->serviceKey = $serviceKey;
        $this->firewallId = $firewallId;
    }
}