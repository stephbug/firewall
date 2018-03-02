<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory;

use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\SecurityModel\Application\Values\SecurityKey;

class SecurityKeyContext
{
    /**
     * @var SecurityKey
     */
    private $securityKey;

    public function __construct(SecurityKey $securityKey)
    {
        $this->securityKey = $securityKey;
    }

    public function key(FirewallContext $context): SecurityKey
    {
        if (!$this->hasSameContext($context) && !$context->isStateless()) {
            return $context->securityKey();
        }

        return $this->securityKey;
    }

    public function originalKey(): SecurityKey
    {
        return $this->securityKey;
    }

    public function toString(FirewallContext $context): string
    {
        return $this->key($context)->value();
    }

    public function hasSameContext(FirewallContext $context): bool
    {
        return $context->securityKey()->sameValueAs($this->securityKey);
    }
}