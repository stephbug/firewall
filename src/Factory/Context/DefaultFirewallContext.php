<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Context;

use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\SecurityModel\Application\Values\Security\AnonymousKey;
use StephBug\SecurityModel\Application\Values\Security\FirewallKey;
use StephBug\SecurityModel\Application\Values\Security\RecallerKey;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;

class DefaultFirewallContext implements FirewallContext
{

    /**
     * @var array
     */
    protected $attributes;

    public function __construct(array $attributes = [])
    {
        if (empty($attributes)) {
            $attributes = [
                'anonymous' => false,
                'stateless' => false,
                'allowToSwitch' => true,
                'securityKey' => 'default_security_key',
                'recallerKey' => 'default_recaller_key',
                'anonymousKey' => 'default_anonymous_key',
                'userProviderId' => 'eloquent',
                'entrypointId' => 'default_entry_point_id',
                'unauthorizedId' => 'default_unauthorized_id',
                'logout' => [],
                'recaller' => []
            ];
        }

        $this->attributes = $attributes;
    }

    public function securityKey(): SecurityKey
    {
        return new FirewallKey($this->attributes['securityKey']);
    }

    public function isStateless(): bool
    {
        return $this->attributes['stateless'];
    }

    public function isAnonymous(): bool
    {
        return $this->attributes['anonymous'];
    }

    public function anonymousKey(): ?AnonymousKey
    {
        if ($this->isAnonymous()) {
            return new AnonymousKey($this->attributes['anonymousKey']);
        }

        return null;
    }

    public function userProviderId(): string
    {
        return $this->attributes['userProviderId'];
    }

    public function entrypointId(): ?string
    {
        return $this->attributes['entrypointId'];
    }

    public function unauthorizedId(): ?string
    {
        return $this->attributes['unauthorizedId'];
    }

    public function hasLogoutKey(string $serviceKey): bool
    {
        return isset($this->attributes['logout']) && isset($this->attributes['logout'][$serviceKey]);
    }

    public function logout(): array
    {
        return $this->attributes['logout'] ?? [];
    }

    public function logoutByKey(string $serviceKey): ?array
    {
        if ($this->hasLogoutKey($serviceKey)) {
            return $this->attributes['logout'][$serviceKey];
        }

        return null;
    }

    public function hasRecaller(string $serviceKey): bool
    {
        return isset($this->attributes['recaller'][$serviceKey]);
    }

    public function recaller(string $serviceKey): ?RecallerKey
    {
        if ($this->hasRecaller($serviceKey)) {
            $key = $this->attributes['recaller'][$serviceKey];

            return new RecallerKey($key);
        }

        return null;
    }

    public function isAllowedToSwitch(): bool
    {
        return $this->attributes['allowToSwitch'];
    }
}