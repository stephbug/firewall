<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Payload;

use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;

class PayloadService
{
    /**
     * @var SecurityKey
     */
    public $securityKey;

    /**
     * @var FirewallContext
     */
    public $context;

    /**
     * @var string
     */
    public $userProviderId;

    /**
     * @var string
     */
    public $entrypoint;

    public function __construct(SecurityKey $securityKey,
                                FirewallContext $context,
                                string $userProviderId,
                                string $entrypoint = null)
    {
        $this->securityKey = $securityKey;
        $this->context = $context;
        $this->userProviderId = $userProviderId;
        $this->entrypoint = $entrypoint;
    }
}