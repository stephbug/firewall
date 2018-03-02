<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory;

use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use StephBug\Firewall\Manager;

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

    public function raise(Collection $middleware, Request $request): Collection
    {
        return $this->process($this->createAuthenticationService($middleware), $request);
    }

    private function createAuthenticationService(Collection $collection): Collection
    {
        $services = new Collection();

        $collection->filter(function (string $name) {
            return $this->manager->hasFirewall($name);
        })->each(function (string $name) use ($services) {
            $services->put($name, $this->manager->guard($name));
        });

        return $services;
    }

    private function process(Collection $services, Request $request): Collection
    {
        return $services->map(function (Builder $builder) use ($request) {
            return $this->pipeline
                ->via('compose')
                ->through($this->bootstraps)
                ->send($builder)
                ->then(function () use ($builder) {
                    return $builder->middleware();
                });
        });
    }
}