<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use StephBug\Firewall\Factory\Builder\Aggregate;
use StephBug\Firewall\Factory\Builder\FirewallMap;
use StephBug\Firewall\Factory\Builder\SecurityContext;
use StephBug\Firewall\Factory\Builder\UserProviders;
use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\Firewall\Factory\Payload\PayloadFactory;

class Builder
{
    /**
     * @var FirewallMap
     */
    private $services;

    /**
     * @var UserProviders
     */
    private $userProviders;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @var Aggregate
     */
    private $aggregate;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $defaultEntrypointId;

    public function __construct(FirewallMap $services,
                                UserProviders $userProviders,
                                SecurityContext $securityContext)
    {
        $this->services = $services;
        $this->userProviders = $userProviders;
        $this->securityContext = $securityContext;
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
        return $this->securityContext->context();
    }

    public function contextKey(): SecurityContext
    {
        return $this->securityContext;
    }

    public function userProviders(): UserProviders
    {
        return $this->userProviders;
    }

    public function authenticationProviders(): array
    {
        return $this->aggregate->providers();
    }

    public function setDefaultEntrypointId(string $entrypointId): void
    {
        $this->defaultEntrypointId = $entrypointId;
    }

    public function defaultEntrypointId(): ?string
    {
        return $this->defaultEntrypointId;
    }

    public function entrypoints(): array
    {
        return $this->aggregate->entrypoints();
    }

    final public function middleware(): array
    {
        return $this->aggregate->firewall();
    }

    public function setRequest(Request $request): Builder
    {
        $this->request = $request;

        return $this;
    }
}