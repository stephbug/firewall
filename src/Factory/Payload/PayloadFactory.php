<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Payload;

class PayloadFactory
{
    /**
     * @var string
     */
    private $firewall;

    /**
     * @var string
     */
    private $provider;

    /**
     * @var string
     */
    private $entrypoint;

    public function setFirewall(string $firewall): PayloadFactory
    {
        $this->firewall = $firewall;

        return $this;
    }

    public function setProvider(string $provider): PayloadFactory
    {
        $this->provider = $provider;

        return $this;
    }

    public function setEntrypoint(string $entrypoint): PayloadFactory
    {
        $this->entrypoint = $entrypoint;

        return $this;
    }

    public function firewall(): ?string
    {
        return $this->firewall;
    }

    public function provider(): ?string
    {
        return $this->provider;
    }

    public function entrypoint(): ?string
    {
        return $this->entrypoint;
    }
}