<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Builder;

use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;

class SecurityContext
{
    /**
     * @var FirewallContext
     */
    private $firewallContext;

    /**
     * @var SecurityKey
     */
    private $securityKey;

    public function __construct(FirewallContext $firewallContext, SecurityKey $securityKey)
    {
        $this->firewallContext = $firewallContext;
        $this->securityKey = $securityKey;
    }

    public function key(): SecurityKey
    {
        if (!$this->hasSameContext() && !$this->firewallContext->isStateless()) {
            return $this->firewallContext->securityKey();
        }

        return $this->securityKey;
    }

    public function hasSameContext(): bool
    {
        return $this->firewallContext->securityKey()->sameValueAs($this->securityKey);
    }

    public function originalKey(): SecurityKey
    {
        return $this->securityKey;
    }

    public function toString(): string
    {
        return $this->key()->value();
    }

    public function context(): FirewallContext
    {
        return $this->firewallContext;
    }
}