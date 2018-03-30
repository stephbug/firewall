<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Builder\SecurityKeyContext;
use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\Firewall\Factory\Payload\PayloadService;
use StephBug\SecurityModel\Application\Http\Event\ContextEvent;
use StephBug\SecurityModel\Application\Http\Firewall\ContextFirewall;
use StephBug\SecurityModel\Application\Values\Providers\UserProviders;
use StephBug\SecurityModel\Guard\Guard;

class SerializationContext extends AuthenticationRegistry
{
    public function compose(Builder $builder, \Closure $make)
    {
        if (!$builder->context()->isStateless()) {
            $serviceId = $this->registerService(
                $this->buildService($builder),
                $builder->userProviders()->toArray()
            );

            $builder((new PayloadFactory())->setFirewall($serviceId));
        }

        return $make($builder);
    }

    protected function registerService(PayloadService $payload, array $userProviders): string
    {
        $serviceId = 'firewall.context_' . $payload->securityKey->value();

        $this->app->bind($serviceId, function (Application $app) use ($userProviders, $payload) {
            return new ContextFirewall(
                $app->make(Guard::class),
                $this->makeUserProviders($userProviders),
                new ContextEvent($payload->securityKey)
            );
        });

        return $serviceId;
    }

    protected function makeUserProviders(array $userProviders): UserProviders
    {
        $collection = new UserProviders();

        foreach ($userProviders as $provider) {
            $collection->add($this->app->make($provider));
        }

        return $collection;
    }

    protected function getServiceId(FirewallContext $context, SecurityKeyContext $keyAware): string
    {
        return 'firewall.context_' . $keyAware->toString($context);
    }
}