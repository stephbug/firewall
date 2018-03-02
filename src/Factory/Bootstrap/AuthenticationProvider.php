<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Contracts\FirewallRegistry;
use StephBug\SecurityModel\Guard\Authentication\AuthenticationProviders;

class AuthenticationProvider implements FirewallRegistry
{
    /**
     * @var AuthenticationProviders
     */
    private $providers;

    /**
     * @var Application
     */
    private $app;

    public function __construct(AuthenticationProviders $providers, Application $app)
    {
        $this->providers = $providers;
        $this->app = $app;
    }

    public function compose(Builder $builder, \Closure $make)
    {
        $bootstrapped = $make($builder);

        if (!$authenticationProviders = $builder->authenticationProviders()) {
            throw new \RuntimeException('No authentication providers has been registered');
        }

        foreach ($authenticationProviders as $provider) {
            $this->providers->add($this->app->make($provider));
        }

        return $bootstrapped;
    }
}