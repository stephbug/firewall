<?php

declare(strict_types=1);

namespace StephBug\Firewall;

use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use StephBug\Firewall\Factory\Builder;

class Factory
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var Pipeline
     */
    private $pipeline;

    /**
     * @var array
     */
    private $bootstraps;

    public function __construct(Manager $manager, Pipeline $pipeline, array $bootstraps)
    {
        $this->manager = $manager;
        $this->pipeline = $pipeline;
        $this->bootstraps = $bootstraps;
    }

    public function raise(array $middleware, Request $request): Collection
    {
        $services = $this->createAuthenticationService($middleware);

        return $this->process($services, $request);
    }

    private function createAuthenticationService(array $middleware): Collection
    {
        $services = new Collection();

        foreach ($middleware as $firewallName) {
            if ($this->manager->hasFirewall($firewallName)) {
                $services->put($firewallName, $this->manager->guard($firewallName));
            }
        }

        return $services;
    }

    private function process(Collection $services, Request $request): Collection
    {
        return $services->map(function (Builder $builder) use ($request) {
            return $this->pipeline
                ->via('compose')
                ->through($this->bootstraps)
                ->send($builder->setRequest($request))
                ->then(function () use ($builder) {
                    return $builder->middleware();
                });
        });
    }
}