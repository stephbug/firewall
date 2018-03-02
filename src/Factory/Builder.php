<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory;

use Illuminate\Support\Collection;
use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\Firewall\Factory\Payload\PayloadFactory;

class Builder
{
    /**
     * @var AuthenticationServices
     */
    private $services;

    /**
     * @var FirewallContext
     */
    private $context;

    /**
     * @var UserProviders
     */
    private $userProviders;

    /**
     * @var SecurityKeyContext
     */
    private $contextKey;

    /**
     * @var Aggregate
     */
    private $aggregate;

    public function __construct(AuthenticationServices $services,
                                FirewallContext $context,
                                UserProviders $userProviders,
                                SecurityKeyContext $contextKey)
    {
        $this->services = $services;
        $this->context = $context;
        $this->userProviders = $userProviders;
        $this->contextKey = $contextKey;
        $this->aggregate = new Aggregate();
    }

    public function __invoke(PayloadFactory $factory): void
    {
        ($this->aggregate)($factory);
    }

    public function getServices(string $position): Collection
    {
        return $this->services->filter($position);
    }

    public function context(): FirewallContext
    {
        return $this->context;
    }

    public function contextKey(): SecurityKeyContext
    {
        return $this->contextKey;
    }

    public function userProviders(): UserProviders
    {
        return $this->userProviders;
    }

    public function authenticationProviders(): array
    {
        return $this->aggregate->providers();
    }

    final public function middleware(): array
    {
        return $this->aggregate->firewall();
    }
}