<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Bootstrap;

use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Contracts\AuthenticationServiceFactory;
use StephBug\Firewall\Factory\Payload\PayloadFactory;

class AuthenticationService extends AuthenticationRegistry
{
    public function compose(Builder $builder, \Closure $make)
    {
        $services = $builder->services();

        $services->map(function ($service) use ($builder) {
            $builder($this->buildFactory($builder, $service));
        });

        return $make($builder);
    }

    protected function buildFactory(Builder $builder, $service): PayloadFactory
    {
        if ($service instanceof AuthenticationServiceFactory) {
            $payload = $this->buildService($builder, $service->userProviderKey());

            return $service->create($payload);
        }

        return (new PayloadFactory())->setFirewall($service);
    }
}