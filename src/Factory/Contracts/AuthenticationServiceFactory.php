<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Contracts;

use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\Firewall\Factory\Payload\PayloadService;
use StephBug\SecurityModel\Application\Http\Request\AuthenticationRequest;

interface AuthenticationServiceFactory
{
    public function create(PayloadService $payload): PayloadFactory;

    public function matcher(): ?AuthenticationRequest;

    public function userProviderKey(): ?string;

    public function serviceKey(): string;
}