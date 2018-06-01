<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Authentication\Generic;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Authentication\HasRecallerFactory;
use StephBug\Firewall\Factory\Contracts\AuthenticationServiceFactory;
use StephBug\Firewall\Factory\Manager\RecallerManager;
use StephBug\Firewall\Factory\Payload\PayloadService;
use StephBug\SecurityModel\Application\Http\Firewall\RecallerAuthenticationFirewall;
use StephBug\SecurityModel\Application\Http\Request\AuthenticationRequest;
use StephBug\SecurityModel\Application\Values\Security\RecallerKey;
use StephBug\SecurityModel\Guard\Authentication\Providers\RecallerAuthenticationProvider;
use StephBug\SecurityModel\Guard\Contract\Guardable;
use StephBug\SecurityModel\User\UserChecker;

class IdentifierPasswordRecallerFactory implements AuthenticationServiceFactory
{
    use HasRecallerFactory;

    /**
     * @var Application
     */
    private $app;

    /**
     * @var RecallerManager
     */
    private $recallerManager;

    public function __construct(Application $app, RecallerManager $recallerManager)
    {
        $this->app = $app;
        $this->recallerManager = $recallerManager;
    }

    protected function registerFirewall(PayloadService $payload, string $recallerServiceId): string
    {
        $id = 'firewall.' . $this->serviceKey() . '_firewall.' . $payload->securityKey->value();

        $this->app->bind($id, function (Application $app) use ($recallerServiceId) {
            return new RecallerAuthenticationFirewall(
                $app->make(Guardable::class),
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

    protected function getRecallerManager(): RecallerManager
    {
        return $this->recallerManager;
    }

    public function serviceKey(): string
    {
        return 'form-login-recaller';
    }

    public function mirrorKey(): string
    {
        return 'form-login';
    }

    public function matcher(): ?AuthenticationRequest
    {
        return null;
    }

    public function userProviderKey(): ?string
    {
        return null;
    }
}