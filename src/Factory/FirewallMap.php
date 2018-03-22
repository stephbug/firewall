<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use StephBug\Firewall\Factory\Contracts\AuthenticationServiceFactory;

class FirewallMap
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

        foreach ($this->map as $serviceKey => $requestMatcher) {
            $this->services->each(function (AuthenticationServiceFactory $factory) use ($mapped, $serviceKey, $requestMatcher, $request) {
                if ($factory->serviceKey() === $serviceKey) {
                    if (null === $requestMatcher || true === $requestMatcher || $requestMatcher->matches($request)) {
                        $mapped->push($factory);
                    }
                }
            });
        }

        return $mapped;
    }
}