<?php

declare(strict_types=1);

namespace StephBug\Firewall\Application\Exception;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthorizationException;
use StephBug\SecurityModel\Application\Exception\InsufficientAuthentication;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorage;
use StephBug\SecurityModel\Guard\Authentication\TrustResolver;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationHandler
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var TrustResolver
     */
    private $trustResolver;

    public function __construct(TokenStorage $tokenStorage, TrustResolver $trustResolver)
    {
        $this->tokenStorage = $tokenStorage;
        $this->trustResolver = $trustResolver;
    }

    public function handle(AuthorizationException $authorizationException,
                           Request $request,
                           ContextualHandler $securityHandler): Response
    {
        $token = $this->tokenStorage->getToken();

        if (!$this->trustResolver->isFullyAuthenticated($token)) {
            return $this->whenUserIsNotFullyAuthenticated($request, $authorizationException, $securityHandler);
        }

        return $this->whenUserIsNotGranted($request, $authorizationException, $securityHandler);
    }

    protected function whenUserIsNotFullyAuthenticated(Request $request,
                                                       AuthorizationException $exception,
                                                       ContextualHandler $contextual): Response
    {
        $message = 'Full authentication is required to access this resource.';
        $authException = new InsufficientAuthentication($message, 0, $exception);

        return $contextual->startAuthentication($request, $authException);
    }

    protected function whenUserIsNotGranted(Request $request,
                                            AuthorizationException $exception,
                                            ContextualHandler $contextual): Response
    {
        if ($denied = $contextual->deniedHandler()) {
            return $denied->handle($request, $exception);
        }

        throw $exception;
    }
}