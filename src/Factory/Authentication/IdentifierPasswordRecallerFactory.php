<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Authentication;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Payload\PayloadService;
use StephBug\SecurityModel\Application\Http\Firewall\RecallerAuthenticationFirewall;
use StephBug\SecurityModel\Application\Values\Security\RecallerKey;
use StephBug\SecurityModel\Guard\Authentication\Providers\RecallerAuthenticationProvider;
use StephBug\SecurityModel\Guard\Guard;
use StephBug\SecurityModel\User\UserChecker;

class IdentifierPasswordRecallerFactory extends RecallerAuthenticationFactory
{
    protected function registerFirewall(PayloadService $payload, string $recallerServiceId): string
    {
        $id = 'firewall.' . $this->serviceKey() . '_listener.' . $payload->securityKey->value();

        $this->app->bind($id, function (Application $app) use ($recallerServiceId) {
            return new RecallerAuthenticationFirewall(
                $app->make(Guard::class),
                $app->make($recallerServiceId)
            );
        });

        return $id;
    }

    protected function registerProvider(PayloadService $payload, RecallerKey $recallerKey): string
    {
        $id = 'firewall.' . $this->serviceKey() . '_provider.' . $payload->securityKey->value();

        $this->app->bind($id, function (Application $app) use ($payload, $recallerKey) {
            return new RecallerAuthenticationProvider(
                $app->make(UserChecker::class),
                $payload->securityKey,
                $recallerKey
            );
        });

        return $id;
    }

    public function serviceKey(): string
    {
        return 'form-login-recaller';
    }

    public function mirrorKey(): string
    {
        return 'form-login';
    }
}