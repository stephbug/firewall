<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Contracts;

use StephBug\SecurityModel\Application\Values\AnonymousKey;
use StephBug\SecurityModel\Application\Values\SecurityKey;

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
}