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
        /**
         * if a default entry point id exists on context
         * we bind it to into the container only if it is not already bound
         * we reset this default entrypoint id on context to make aware all concerned services
         */
        if ($entrypoint = $builder->context()->entrypointId()) {
            if (!$this->app->bound($entrypoint)) {
                $serviceId = 'firewall.default_entrypoint.' . $builder->contextKey()->toString($builder->context());

                $this->app->bind($serviceId, $entrypoint);

                $builder->context()->setEntrypointId($serviceId);
            }
        }

        return $make($builder);
    }
}