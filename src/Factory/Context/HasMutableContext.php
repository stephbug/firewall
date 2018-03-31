<?php

namespace StephBug\Firewall\Factory\Context;

use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\SecurityModel\Application\Values\Security\RecallerKey;

trait HasMutableContext
{
    public function setStateless(bool $stateless): FirewallContext
    {
        $this->setAttribute('stateless', $stateless);

        return $this;
    }

    public function setAnonymous(bool $anonymous): FirewallContext
    {
        $this->setAttribute('anonymous', $anonymous);

        return $this;
    }

    public function setAnonymousKey(string $anonymousKey): FirewallContext
    {
        $this->setAttribute('anonymousKey', $anonymousKey);

        return $this;
    }

    public function setEntrypointId(string $entrypointId): FirewallContext
    {
        $this->setAttribute('entrypointId', $entrypointId);

        return $this;
    }

    public function setUnauthorizedId(string $unauthorizedId): FirewallContext
    {
        $this->setAttribute('unauthorizedId', $unauthorizedId);

        return $this;
    }

    public function addLogout(string $serviceKey, array $payload): FirewallContext
    {
        $this->setAttribute('logout', [$serviceKey => $payload]);

        return $this;
    }

    public function addRecaller(string $serviceKey, string $recallerKey): FirewallContext
    {
        $this->setAttribute('recaller', [$serviceKey => $recallerKey]);

        return $this;
    }

    public function allowToSwitch(bool $allowToSwitch): FirewallContext
    {
        $this->setAttribute('allowToSwitch', $allowToSwitch);

        return $this;
    }

    abstract protected function setAttribute(string $name, $value): void;
}