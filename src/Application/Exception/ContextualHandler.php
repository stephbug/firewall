<?php

declare(strict_types=1);

namespace StephBug\Firewall\Application\Exception;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Application\Exception\InvalidUserStatus;
use StephBug\SecurityModel\Application\Http\Entrypoint\Entrypoint;
use StephBug\SecurityModel\Application\Http\Response\Unauthorized;
use StephBug\SecurityModel\Application\Values\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorage;
use StephBug\SecurityModel\Guard\Authentication\TrustResolver;
use Symfony\Component\HttpFoundation\Response;

class ContextualHandler
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var SecurityKey
     */
    private $securityKey;

    /**
     * @var TrustResolver
     */
    private $trustResolver;

    /**
     * @var bool
     */
    private $stateless;

    /**
     * @var Entrypoint
     */
    private $entrypoint;

    /**
     * @var Unauthorized
     */
    private $deniedHandler;

    public function __construct(TokenStorage $tokenStorage,
                                SecurityKey $securityKey,
                                TrustResolver $trustResolver,
                                bool $stateless,
                                Entrypoint $entrypoint = null,
                                Unauthorized $deniedHandler = null)
    {
        $this->tokenStorage = $tokenStorage;
        $this->securityKey = $securityKey;
        $this->trustResolver = $trustResolver;
        $this->stateless = $stateless;
        $this->entrypoint = $entrypoint;
        $this->deniedHandler = $deniedHandler;
    }

    public function startAuthentication(Request $request, AuthenticationException $exception): Response
    {
        if (!$this->entrypoint) {
            throw $exception;
        }

        if ($exception instanceof InvalidUserStatus) {
            $this->tokenStorage->setToken(null);
        }

        return $this->entrypoint->startAuthentication($request, $exception);
    }

    public function setEntrypoint(Entrypoint $entrypoint): ContextualHandler
    {
        $this->entrypoint = $entrypoint;

        return $this;
    }

    public function setUnauthorized(Unauthorized $authorizationDenied): ContextualHandler
    {
        $this->deniedHandler = $authorizationDenied;

        return $this;
    }

    public function deniedHandler(): ?Unauthorized
    {
        return $this->deniedHandler;
    }

    public function entrypoint(): ?Entrypoint
    {
        return $this->entrypoint;
    }
}