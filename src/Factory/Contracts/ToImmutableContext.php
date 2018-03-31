<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Contracts;

interface ToImmutableContext extends FirewallContext
{
    public function toImmutable(): FirewallContext;
}