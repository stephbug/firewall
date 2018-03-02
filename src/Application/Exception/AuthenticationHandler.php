<?php

declare(strict_types=1);

namespace StephBug\Firewall\Application\Exception;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationHandler
{
    public function handle(AuthenticationException $authenticationException,
                           Request $request,
                           ContextualHandler $securityHandler): Response
    {
        return $securityHandler->startAuthentication($request, $authenticationException);
    }
}