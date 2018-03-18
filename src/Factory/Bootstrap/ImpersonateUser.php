<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Contracts\FirewallRegistry;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\Firewall\Factory\Payload\PayloadService;
use StephBug\SecurityModel\Application\Http\Firewall\SwitchUserAuthenticationFirewall;
use StephBug\SecurityModel\Application\Http\Request\SwitchUserAuthenticationRequest;
use StephBug\SecurityModel\Guard\Authorization\Grantable;
use StephBug\SecurityModel\Guard\Guard;

class ImpersonateUser implements FirewallRegistry
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
        if ($builder->context()->isAllowedToSwitch()) {

            $payload = $this->buildPayload($builder);
            $serviceId = 'firewall.impersonate_user_firewall.' . $payload->securityKey;

            $this->app->bind($serviceId, function (Application $app) use ($payload) {
                return new SwitchUserAuthenticationFirewall(
                    $app->make(Guard::class),
                    $app->make(Grantable::class),
                    new SwitchUserAuthenticationRequest(),
                    $app->make($payload->userProviderId),
                    $payload->securityKey,
                    $payload->context->isStateless()
                );
            });

            $builder((new PayloadFactory())->setFirewall($serviceId));
        }

        return $make($builder);
    }

    protected function buildPayload(Builder $builder): PayloadService
    {
        return new PayloadService(
            $builder->contextKey()->key($builder->context()),
            $builder->context(),
            $builder->userProviders()->get($builder->context()),
            $builder->context()->entrypointId()
        );
    }
}