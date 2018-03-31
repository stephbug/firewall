<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Contracts;

interface MutableContext extends FirewallContext
{
    public function setStateless(bool $stateless): MutableContext;

    public function setAnonymous(bool $anonymous): MutableContext;

    public function setAnonymousKey(string $anonymousKey): MutableContext;

    public function setEntrypointId(string $entrypointId): MutableContext;

    public function setUnauthorizedId(string $unauthorizedId): MutableContext;

    public function addLogout(string $serviceKey, array $payload): MutableContext;

    public function addRecaller(string $serviceKey, string $recallerKey): MutableContext;

    public function allowToSwitch(bool $allowToSwitch): MutableContext;

    public function setAttribute(string $name, $value): void;
}