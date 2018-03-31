<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Contracts;

use StephBug\SecurityModel\Application\Values\Security\AnonymousKey;
use StephBug\SecurityModel\Application\Values\Security\RecallerKey;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;

interface FirewallContext
{
    public function securityKey(): SecurityKey;

    public function anonymousKey(): ?AnonymousKey;

    public function isStateless(): bool;

    public function isAnonymous(): bool;

    public function userProviderId(): string;

    public function entrypointId(): ?string;

    public function unauthorizedId(): ?string;

    public function hasLogoutKey(string $serviceKey): bool;

    public function logout(): array;

    public function logoutByKey(string $serviceKey): ?array;

    public function hasRecaller(string $serviceKey): bool;

    public function recaller(string $serviceKey): ?RecallerKey;

    public function isAllowedToSwitch(): bool;

    public function getAttributes(): array;
}