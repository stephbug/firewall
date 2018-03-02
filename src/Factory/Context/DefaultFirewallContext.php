<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Context;

use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\SecurityModel\Application\Values\AnonymousKey;
use StephBug\SecurityModel\Application\Values\FirewallKey;
use StephBug\SecurityModel\Application\Values\SecurityKey;

class DefaultFirewallContext implements FirewallContext
{
    /**
     * @var string
     */
    private $securityKey;

    /**
     * @var string
     */
    private $anonymousKey;

    /**
     * @var string
     */
    private $userProviderId;

    /**
     * @var string
     */
    private $entrypointId;

    /**
     * @var string
     */
    private $unauthorizedId;

    /**
     * @var bool
     */
    private $anonymous = false;

    /**
     * @var bool
     */
    private $stateless = true;

    public function securityKey(): SecurityKey
    {
        return new FirewallKey($this->securityKey);
    }

    public function setStateless(bool $stateless): FirewallContext
    {
        $this->stateless = $stateless;

        return $this;
    }

    public function isStateless(): bool
    {
        return $this->stateless;
    }

    public function setAnonymous(bool $anonymous): FirewallContext
    {
        $this->anonymous = $anonymous;

        return $this;
    }

    public function isAnonymous(): bool
    {
        return $this->anonymous;
    }

    public function setAnonymousKey(string $anonymousKey): FirewallContext
    {
        $this->anonymousKey = $anonymousKey;

        return $this;
    }

    public function anonymousKey(): AnonymousKey
    {
        return new AnonymousKey($this->anonymousKey);
    }

    public function userProviderId(): string
    {
        return $this->userProviderId;
    }

    public function setEntrypointId(string $entrypointId): FirewallContext
    {
        $this->entrypointId = $entrypointId;

        return $this;
    }

    public function entrypointId(): ?string
    {
        return $this->entrypointId;
    }

    public function unauthorizedId(): ?string
    {
        return $this->unauthorizedId;
    }

    public function setUnauthorizedId(string $unauthorizedId): FirewallContext
    {
        $this->unauthorizedId = $unauthorizedId;

        return $this;
    }
}