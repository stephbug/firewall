<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory;

use StephBug\Firewall\Factory\Payload\PayloadFactory;

class Aggregate
{
    /**
     * @var array
     */
    private $firewall = [];

    /**
     * @var array
     */
    private $providers = [];

    /**
     * @var array
     */
    private $entrypoints = [];

    public function __invoke(PayloadFactory $payload): void
    {
        if ($payload->firewall()) {
            $this->firewall[] = $payload->firewall();
        }

        if ($payload->provider()) {
            $this->providers[] = $payload->provider();
        }

        if ($payload->entrypoint()) {
            $this->entrypoints[] = $payload->entrypoint();
        }
    }

    public function firewall(): array
    {
        return $this->firewall;
    }

    public function providers(): array
    {
        return $this->providers;
    }

    public function entrypoints(): array
    {
        return $this->entrypoints;
    }
}