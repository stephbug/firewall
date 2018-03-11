<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use StephBug\Firewall\Factory\Contracts\AuthenticationServiceFactory;

class AuthenticationServices
{
    /**
     * @var array
     */
    private $services;

    /**
     * @var Request
     */
    private $request;

    public function __construct(array $services = null, Request $request = null)
    {
        $this->services = new Collection($services ?? []);
        $this->request = $request;
    }

    public function add(string $serviceKey, AuthenticationServiceFactory $serviceFactory): self
    {
        $this->services->put($serviceKey, $serviceFactory);

        return $this;
    }

    public function filter(string $position): Collection
    {
        return $this->filterByMatcher($this->filterByPosition($position));
    }

    protected function filterByPosition(string $position): Collection
    {
        return $this->services->filter(function (AuthenticationServiceFactory $serviceFactory) use ($position) {
            return $position === $serviceFactory->position();
        });
    }

    protected function filterByMatcher(Collection $services): Collection
    {
        if ($this->request) {
            return $services->filter(function (AuthenticationServiceFactory $serviceFactory) {
                return $serviceFactory->matcher()->matches($this->request);
            });
        }

        return $services;
    }
}