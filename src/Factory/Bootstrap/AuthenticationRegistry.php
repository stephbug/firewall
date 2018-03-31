<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Contracts\FirewallRegistry;
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
            $builder->contextKey()->key($context),
            $context,
            $builder->userProviders()->get($context, $userProviderKey),
            $builder->defaultEntrypointId()
        );
    }
}