<?php

declare(strict_types=1);

namespace StephBug\Firewall;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\AuthenticationServices;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\Firewall\Factory\SecurityKeyContext;
use StephBug\Firewall\Factory\UserProviders;
use StephBug\SecurityModel\Application\Values\FirewallKey;

class Manager
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var array
     */
    protected $guards = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function guard(string $name): Builder
    {
        if (!$this->hasFirewall($name)) {
            throw new \RuntimeException(sprintf('no config defined for guard name %s', $name));
        }

        return $this->guards[$name] ?? $this->guards[$name] = $this->resolve($name);
    }

    protected function resolve(string $name): Builder
    {
        return new Builder(
            $this->resolveFactories($name),
            $this->resolveFirewallContext($name),
            $this->resolveUserProviders(),
            new SecurityKeyContext(new FirewallKey($name))
        );
    }

    protected function resolveFirewallContext(string $name): FirewallContext
    {
        $context = $this->getConfig('context.' . $name) ?? $this->getConfig('context.default');

        if (!$context) {
            throw new \RuntimeException(sprintf('Firewall context missing for firewall name %', $name));
        }

        return $this->app->make($context);
    }

    protected function resolveUserProviders(): UserProviders
    {
        return new UserProviders($this->getConfig('user_providers'));
    }

    protected function resolveFactories(string $name): AuthenticationServices
    {
        $services = $this->getConfig('services.' . $name, []);

        $factories = [];

        foreach ($services as $serviceKey => $serviceId) {
            $factories[] = $this->app->make($serviceId);
        }

        return new AuthenticationServices($factories);
    }

    public function hasFirewall(string $name): bool
    {
        return isset($this->guards[$name]) || null !== $this->getConfig('services.' . $name);
    }

    protected function getConfig(string $key, $default = null)
    {
        return $this->app->make('config')->get(sprintf('firewall.%s', $key), $default);
    }
}