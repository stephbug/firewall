<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Payload\PayloadService;
use StephBug\SecurityModel\Application\Http\Firewall\SwitchUserAuthenticationFirewall;
use StephBug\SecurityModel\Application\Http\Request\SwitchUserAuthenticationRequest;
use StephBug\SecurityModel\Guard\Authorization\Grantable;
use StephBug\SecurityModel\Guard\Contract\Guardable;

class ImpersonateUser extends AuthenticationRegistry
{
    public function compose(Builder $builder, \Closure $make)
    {
        $firewallContext = $builder->context();

        if ($firewallContext->isAllowedToSwitch() || !$firewallContext->isStateless()) {
            $serviceId = $this->registerImpersonateUser(
                $this->buildService($builder)
            );

            $this->registerFirewall($serviceId, $builder);
        }

        return $make($builder);
    }

    private function registerImpersonateUser(PayloadService $payload): string
    {
        $serviceId = 'firewall.impersonate_user_firewall.' . $payload->securityKey->value();

        $this->app->bind($serviceId, function (Application $app) use ($payload) {
            return new SwitchUserAuthenticationFirewall(
                $app->make(Guardable::class),
                $app->make(Grantable::class),
                new SwitchUserAuthenticationRequest(),
                $app->make($payload->userProviderId),
                $payload->securityKey,
                $payload->context->isStateless()
            );
        });

        return $serviceId;
    }
}