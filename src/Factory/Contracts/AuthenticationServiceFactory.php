<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Contracts;

use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\Firewall\Factory\Payload\PayloadService;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

interface AuthenticationServiceFactory
{
    public function create(PayloadService $payload): PayloadFactory;

    public function position(): string;

    public function matcher(): ?RequestMatcherInterface;

    public function userProviderKey(): ?string;

    public function serviceKey(): string;
}