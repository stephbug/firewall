<?php

declare(strict_types=1);

namespace StephBug\Firewall\Application\Exception;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Application\Exception\AuthorizationException;
use StephBug\SecurityModel\Application\Exception\SecurityException;
use StephBug\SecurityModel\Application\Http\Entrypoint\Entrypoint;
use Symfony\Component\HttpFoundation\Response;

class SecurityHandler implements DebugFirewall
{
    /**
     * @var ContextualHandler
     */
    private $contextualHandler;

    /**
     * @var AuthenticationHandler
     */
    private $authenticationHandler;

    /**
     * @var AuthorizationHandler
     */
    private $authorizationHandler;

    public function __construct(ContextualHandler $contextualHandler,
                                AuthenticationHandler $authenticationHandler,
                                AuthorizationHandler $authorizationHandler)
    {
        $this->contextualHandler = $contextualHandler;
        $this->authenticationHandler = $authenticationHandler;
        $this->authorizationHandler = $authorizationHandler;
    }

    public function handle(Request $request, SecurityException $securityException): Response
    {
        if ($securityException instanceof AuthenticationException) {
            return $this->authenticationHandler->handle($securityException, $request, $this->contextualHandler);
        }

        if ($securityException instanceof AuthorizationException) {
            return $this->authorizationHandler->handle($securityException, $request, $this->contextualHandler);
        }

        throw $securityException;
    }

    public function setEntrypoint(Entrypoint $entrypoint): void
    {
        $this->contextualHandler->setEntrypoint($entrypoint);
    }
}