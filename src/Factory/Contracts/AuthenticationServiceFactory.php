<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Contracts;

use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\Firewall\Factory\Payload\PayloadService;
use Symfony\Component\HttpFoundation\RequestMatcher;

interface AuthenticationServiceFactory
{
    public function create(PayloadService $payload): PayloadFactory;

    public function registerProvider(): string;

    public function registerFirewall(): string;

    public function registerEntrypoint(): ?string;

    public function position(): string;

    public function matcher(): ?RequestMatcher;

    public function userProviderKey(): ?string;
}