<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Contracts\AuthenticationServiceFactory;
use StephBug\Firewall\Factory\Contracts\FirewallRegistry;
use StephBug\Firewall\Factory\Payload\PayloadService;

class AuthenticationService implements FirewallRegistry
{
    /**
     * @var Application
     */
    private $app;

    public static $positions = ['pre_auth', 'form', 'http', 'remember_me', 'logout'];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function compose(Builder $builder, \Closure $make)
    {
        array_walk(static::$positions, function (string $position) use ($builder) {
            $builder
                ->getServices($position)
                ->map(function (AuthenticationServiceFactory $serviceFactory) use ($builder) {
                    $builder($serviceFactory->create(
                        $this->registerService($builder, $serviceFactory->userProviderKey())
                    ));
                });
        });

        return $make($builder);
    }

    protected function registerService(Builder $builder, string $userProviderKey = null): PayloadService
    {
        $context= $builder->context();

        return new PayloadService(
            $builder->contextKey()->key($context),
            $context,
            $builder->userProviders()->get($context, $userProviderKey),
            $context->entrypointId()
        );
    }
}