<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Mock;

use StephBug\Firewall\Factory\Contracts\AuthenticationServiceFactory;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\Firewall\Factory\Payload\PayloadService;
use StephBug\SecurityModel\Application\Http\Request\AuthenticationRequest;

class SomeAuthenticationServiceFactory implements AuthenticationServiceFactory
{
    public function create(PayloadService $payload): PayloadFactory
    {
       return (new PayloadFactory())
           ->setFirewall('foo_firewall')
           ->setProvider('foo_provider')
           ->setEntrypoint('foo_entrypoint');
    }

    public function matcher(): ?AuthenticationRequest
    {
        return null;
    }

    public function userProviderKey(): ?string
    {
        return 'provider_key';
    }

    public function serviceKey(): string
    {
        return 'service_key';
    }
}