<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Contracts;

interface MutableFirewallContext extends FirewallContext
{
    public function setStateless(bool $stateless): FirewallContext;

    public function setAnonymous(bool $anonymous): FirewallContext;

    public function setAnonymousKey(string $anonymousKey): FirewallContext;

    public function setEntrypointId(string $entrypointId): FirewallContext;

    public function setUnauthorizedId(string $unauthorizedId): FirewallContext;

    public function addLogout(string $serviceKey, array $payload): FirewallContext;

    public function addRecaller(string $serviceKey, string $recallerKey): FirewallContext;

    public function allowToSwitch(bool $allowToSwitch): FirewallContext;
}