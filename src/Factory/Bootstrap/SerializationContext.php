<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\Firewall\Factory\Contracts\FirewallRegistry;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\Firewall\Factory\SecurityKeyContext;
use StephBug\SecurityModel\Application\Http\Event\ContextEvent;
use StephBug\SecurityModel\Application\Http\Firewall\ContextFirewall;
use StephBug\SecurityModel\Application\Values\SecurityKey;
use StephBug\SecurityModel\Application\Values\UserProviders;
use StephBug\SecurityModel\Guard\Guard;

class SerializationContext implements FirewallRegistry
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
        $context = $builder->context();
        $contextKey = $builder->contextKey();

        if (!$context->isStateless()) {
            $serviceId = $this->getServiceId($context, $contextKey);

            $this->registerService($serviceId, $builder->userProviders()->toArray(), $contextKey->key($context));

            $builder((new PayloadFactory())->setFirewall($serviceId));
        }

        return $make($builder);
    }

    protected function registerService(string $serviceId, array $userProviders, SecurityKey $securityKey): void
    {
        $this->app->bind($serviceId, function (Application $app) use ($userProviders, $securityKey) {
            return new ContextFirewall(
                $app->make(Guard::class),
                $this->makeUserProviders($userProviders),
                new ContextEvent($securityKey)
            );
        });
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