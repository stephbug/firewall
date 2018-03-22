<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use StephBug\Firewall\Manager;
use StephBug\Firewall\Processor;

class Factory
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var array
     */
    private $map;

    public function __construct(Manager $manager, Processor $processor, array $map = [])
    {
        $this->manager = $manager;
        $this->processor = $processor;
        $this->map = $map;
    }

    public function raise(Collection $middleware, Request $request): Collection
    {
        $services = $this->createAuthenticationService($middleware);

        return $this->processor->process($services, $request);
    }

    private function createAuthenticationService(Collection $collection): Collection
    {
        $services = new Collection();

        $collection
            ->filter(function (string $name) {
                return $this->manager->hasFirewall($name);
            })
            ->each(function (string $name) use ($services) {
                $services->put($name, $this->manager->guard($name));
            });

        return $services;
    }
}