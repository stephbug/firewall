<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Contracts\FirewallRegistry;

class EntrypointRegistry implements FirewallRegistry
{
    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function compose(Builder $builder, \Closure $make)
    {
        if ($entrypoint = $builder->context()->entrypointId()) {

            $serviceId = $this->determineEntrypointId($builder, $entrypoint);

            $this->app->bind($serviceId, $entrypoint);

            $builder->setDefaultEntrypointId($serviceId);
        }

        return $make($builder);
    }

    private function determineEntrypointId(Builder $builder, string $entrypoint): string
    {
        return $this->app->bound($entrypoint)
            ? $entrypoint
            : 'firewall.default_entrypoint.' . $builder->contextKey()->toString($builder->context());
    }
}