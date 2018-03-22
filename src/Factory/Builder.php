<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\Firewall\Factory\Payload\PayloadFactory;

class Builder
{
    /**
     * @var ServiceMap
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

    /**
     * @var Request
     */
    private $request;

    public function __construct(ServiceMap $services,
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

    public function services(): Collection
    {
        return $this->services->matches($this->request);
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

    public function entrypoints(): array
    {
        return $this->aggregate->entrypoints();
    }

    public function setRequest(Request $request): Builder
    {
        $this->request = $request;

        return $this;
    }

    final public function middleware(): array
    {
        return $this->aggregate->firewall();
    }
}