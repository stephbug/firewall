<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Contracts\FirewallRegistry;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\Firewall\Factory\Payload\PayloadService;

abstract class AuthenticationRegistry implements FirewallRegistry
{
    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    protected function buildService(Builder $builder, string $userProviderKey = null): PayloadService
    {
        $context = $builder->context();

        return new PayloadService(
            $builder->contextKey()->key(),
            $context,
            $builder->userProviders()->get($context, $userProviderKey),
            $builder->defaultEntrypointId()
        );
    }

    protected function registerFirewall(string $firewallId, Builder $builder): void
    {
        $builder($this->buildFactory()->setFirewall($firewallId));
    }

    protected function registerProvider(string $providerId, Builder $builder): void
    {
        $builder($this->buildFactory()->setProvider($providerId));
    }

    protected function buildFactory(): PayloadFactory
    {
        return new PayloadFactory();
    }
}