<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Builder;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use StephBug\Firewall\Factory\Contracts\AuthenticationServiceFactory;

class FirewallMap
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var array
     */
    private $map;

    public function __construct(Application $app, array $map = [])
    {
        $this->app = $app;
        $this->map = $map;
    }

    public function matches(Request $request): Collection
    {
        $mapped = new Collection();

        foreach ($this->map as $service) {
            if ($result = $this->resolveIfFactory($service, $request)) {
                $mapped->push($result);
            }
        }

        return $mapped;
    }

    /**
     * @param string|array $service
     * @param Request $request
     * @return string|AuthenticationServiceFactory|null
     */
    protected function resolveIfFactory($service, Request $request)
    {
        if (is_string($service)) {
            return $service;
        }

        [$serviceKey, $serviceFactory, $requestMatcher] = $service;

        $serviceFactory = $this->app->make($serviceFactory);

        if (is_string($requestMatcher)) {
            $requestMatcher = $this->app->make($requestMatcher);
        }

        if ($serviceFactory->serviceKey() === $serviceKey && $requestMatcher) {
            if (true === $requestMatcher || $requestMatcher->matches($request)) {
                return $serviceFactory;
            }
        }

        return null;
    }
}