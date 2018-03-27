<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use StephBug\Firewall\Factory\Contracts\AuthenticationServiceFactory;

class ServiceMap
{
    /**
     * @var Collection
     */
    private $services;

    /**
     * @var array
     */
    private $map;

    public function __construct(Collection $services, array $map = [])
    {
        $this->services = $services;
        $this->map = $map;
    }

    public function matches(Request $request): Collection
    {
        $mapped = new Collection();

        foreach ($this->map as $serviceKey => $service) {
            $this->services->each(function (AuthenticationServiceFactory $factory) use ($mapped, $request, $service) {
                [$serviceKey, $requestMatcher] = $service;

                if ($factory->serviceKey() === $serviceKey && $requestMatcher) {
                    if (true === $requestMatcher || $requestMatcher->matches($request)) {
                        $mapped->push($factory);
                    }
                }
            });
        }

        return $mapped;
    }
}