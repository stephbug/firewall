<?php

namespace StephBug\Firewall\Factory\Context;

use StephBug\Firewall\Factory\Contracts\MutableContext as BaseMutable;

trait HasMutableContext
{
    public function setStateless(bool $stateless): BaseMutable
    {
        $this->setAttribute('stateless', $stateless);

        return $this;
    }

    public function setAnonymous(bool $anonymous): BaseMutable
    {
        $this->setAttribute('anonymous', $anonymous);

        return $this;
    }

    public function setAnonymousKey(string $anonymousKey): BaseMutable
    {
        $this->setAttribute('anonymousKey', $anonymousKey);

        return $this;
    }

    public function setEntrypointId(string $entrypointId): BaseMutable
    {
        $this->setAttribute('entrypointId', $entrypointId);

        return $this;
    }

    public function setUnauthorizedId(string $unauthorizedId): BaseMutable
    {
        $this->setAttribute('unauthorizedId', $unauthorizedId);

        return $this;
    }

    public function addLogout(string $serviceKey, array $payload): BaseMutable
    {
        $this->setAttribute('logout', [$serviceKey => $payload]);

        return $this;
    }

    public function addRecaller(string $serviceKey, string $recallerKey): BaseMutable
    {
        $this->setAttribute('recaller', [$serviceKey => $recallerKey]);

        return $this;
    }

    public function allowToSwitch(bool $allowToSwitch): BaseMutable
    {
        $this->setAttribute('allowToSwitch', $allowToSwitch);

        return $this;
    }
}