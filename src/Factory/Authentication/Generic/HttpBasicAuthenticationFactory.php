<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Authentication\Generic;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Hashing\Hasher;
use StephBug\Firewall\Factory\Contracts\AuthenticationServiceFactory;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\Firewall\Factory\Payload\PayloadService;
use StephBug\SecurityModel\Application\Http\Firewall\HttpBasicAuthenticationFirewall;
use StephBug\SecurityModel\Application\Http\Request\AuthenticationRequest;
use StephBug\SecurityModel\Application\Http\Request\HttpBasicAuthenticationRequest;
use StephBug\SecurityModel\Guard\Authentication\Providers\IdentifierPasswordAuthenticationProvider;
use StephBug\SecurityModel\Guard\Contract\Guardable;
use StephBug\SecurityModel\User\UserChecker;

class HttpBasicAuthenticationFactory implements AuthenticationServiceFactory
{
    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function create(PayloadService $payload): PayloadFactory
    {
        return (new PayloadFactory())
            ->setFirewall($this->registerFirewall($payload))
            ->setProvider($this->registerProvider($payload));
    }

    protected function registerFirewall(PayloadService $payload): string
    {
        $id = 'firewall.basic_firewall.' . $payload->securityKey->value();

        $this->app->bind($id, function (Application $app) use ($payload) {
            return new HttpBasicAuthenticationFirewall(
                $app->make(Guardable::class),
                $this->matcher(),
                $app->make($payload->context->entrypointId()),
                $payload->securityKey
            );
        });

        return $id;
    }

    protected function registerProvider(PayloadService $payload): string
    {
        $id = 'firewall.generic_provider.' . $payload->securityKey->value();

        $this->app->bind($id, function (Application $app) use ($payload) {
            return new IdentifierPasswordAuthenticationProvider(
                $app->make($payload->userProviderId),
                $app->make(UserChecker::class),
                $payload->securityKey,
                $app->make(Hasher::class)
            );
        });

        return $id;
    }

    public function matcher(): ?AuthenticationRequest
    {
        return new HttpBasicAuthenticationRequest();
    }

    public function userProviderKey(): ?string
    {
        return null;
    }

    public function serviceKey(): string
    {
        return 'http-basic';
    }
}