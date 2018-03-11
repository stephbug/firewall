<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Context;

use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\SecurityModel\Application\Values\AnonymousKey;
use StephBug\SecurityModel\Application\Values\FirewallKey;
use StephBug\SecurityModel\Application\Values\RecallerKey;
use StephBug\SecurityModel\Application\Values\SecurityKey;

class DefaultFirewallContext implements FirewallContext
{
    /**
     * @var string
     */
    protected $securityKey;

    /**
     * @var string
     */
    protected $anonymousKey;

    /**
     * @var string
     */
    protected $userProviderId;

    /**
     * @var string
     */
    protected $entrypointId;

    /**
     * @var string
     */
    protected $unauthorizedId;

    /**
     * @var bool
     */
    protected $anonymous = false;

    /**
     * @var bool
     */
    protected $stateless = true;

    /**
     * @var array
     */
    protected $logout = [];

    /**
     * @var array
     */
    protected $recaller = [];

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

    public function addLogout(string $serviceKey, array $payload): FirewallContext
    {
        $this->logout[$serviceKey] = $payload;

        return $this;
    }

    public function hasLogoutKey(string $serviceKey): bool
    {
        return isset($this->logout[$serviceKey]);
    }

    public function logout(string $serviceKey): ?array
    {
        return $this->logout[$serviceKey] ?? null;
    }

    public function addRecaller(string $serviceKey, string $recallerKey): FirewallContext
    {
        $this->recaller[$serviceKey] = new RecallerKey($recallerKey);

        return $this;
    }

    public function hasRecaller(string $serviceKey): bool
    {
        return isset($this->recaller[$serviceKey]);
    }

    public function recaller(string $serviceKey): ?RecallerKey
    {
        return $this->recaller[$serviceKey] ?? null;
    }
}