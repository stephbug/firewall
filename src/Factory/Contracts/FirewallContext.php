<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Contracts;

use StephBug\SecurityModel\Application\Values\Security\AnonymousKey;
use StephBug\SecurityModel\Application\Values\Security\RecallerKey;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;

interface FirewallContext
{
    public function securityKey(): SecurityKey;

    public function setStateless(bool $stateless): FirewallContext;

    public function isStateless(): bool;

    public function setAnonymous(bool $anonymous): FirewallContext;

    public function isAnonymous(): bool;

    public function setAnonymousKey(string $anonymousKey): FirewallContext;

    public function anonymousKey(): AnonymousKey;

    public function userProviderId(): string;

    public function entrypointId(): ?string;

    public function setEntrypointId(string $entrypointId): FirewallContext;

    public function unauthorizedId(): ?string;

    public function setUnauthorizedId(string $unauthorizedId): FirewallContext;

    public function addLogout(string $serviceKey, array $payload): FirewallContext;

    public function hasLogoutKey(string $serviceKey): bool;

    public function logout(): array;

    public function logoutByKey(string $serviceKey): ?array;

    public function addRecaller(string $serviceKey, string $recallerKey): FirewallContext;

    public function hasRecaller(string $serviceKey): bool;

    public function recaller(string $serviceKey): ?RecallerKey;

    public function isAllowedToSwitch(): bool;

    public function setAllowToSwitch(bool $allowToSwitch): FirewallContext;
}