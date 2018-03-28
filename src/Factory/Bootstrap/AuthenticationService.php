<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Contracts\AuthenticationServiceFactory;
use StephBug\Firewall\Factory\Contracts\FirewallRegistry;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\Firewall\Factory\Payload\PayloadService;

class AuthenticationService implements FirewallRegistry
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
        $builder->services()
            ->map(function ($serviceFactory) use ($builder) {
                if ($serviceFactory instanceof AuthenticationServiceFactory) {
                    $payload = $serviceFactory->create(
                        $this->registerService($builder, $serviceFactory->userProviderKey())
                    );
                } else {
                    $payload = (new PayloadFactory())->setFirewall($serviceFactory);
                }

                $builder($payload);
            });

        return $make($builder);
    }

    protected function registerService(Builder $builder, string $userProviderKey = null): PayloadService
    {
        $context = $builder->context();

        return new PayloadService(
            $builder->contextKey()->key($context),
            $context,
            $builder->userProviders()->get($context, $userProviderKey),
            $context->entrypointId()
        );
    }
}