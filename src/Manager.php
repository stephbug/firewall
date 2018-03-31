<?php

declare(strict_types=1);

namespace StephBug\Firewall;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Builder\FirewallMap;
use StephBug\Firewall\Factory\Builder\SecurityKeyContext;
use StephBug\Firewall\Factory\Builder\UserProviders;
use StephBug\Firewall\Factory\Context\ImmutableContext;
use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\Firewall\Factory\Contracts\ToImmutableContext;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;
use StephBug\SecurityModel\Application\Values\Security\FirewallKey;

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
            throw InvalidArgument::reason(sprintf('no configuration defined for guard name %s', $name));
        }

        return $this->guards[$name] ?? $this->guards[$name] = $this->resolve($name);
    }

    protected function resolve(string $name): Builder
    {
        return new Builder(
            $this->requireMap($name),
            $this->resolveContext($name),
            $this->userProviders(),
            new SecurityKeyContext(new FirewallKey($name))
        );
    }

    protected function resolveContext(string $name): FirewallContext
    {
        $context = $this->getConfig('services.' . $name . '.context');

        if (!$context) {
            throw InvalidArgument::reason(sprintf('Firewall context missing for firewall name %', $name));
        }

        $context = $this->app->make($context);

        if ($context instanceof ToImmutableContext) {
            return new ImmutableContext($context->getAttributes());
        }

        return $context;
    }

    protected function userProviders(): UserProviders
    {
        return new UserProviders($this->getConfig('user_providers'));
    }

    protected function requireMap(string $name): FirewallMap
    {
        $map = $this->getConfig('services.' . $name . '.map', []);

        if (!$map) {
            throw InvalidArgument::reason(sprintf('Register at least one service in map configuration %', $name));
        }

        return new FirewallMap($this->app, $map);
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