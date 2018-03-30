<?php

declare(strict_types=1);

namespace StephBug\Firewall\Application\Http\Middleware;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Http\Event\ContextEvent;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorage;
use StephBug\SecurityModel\Guard\Authentication\TrustResolver;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\TerminableInterface;

class SessionContext implements TerminableInterface
{
    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    /**
     * @var TrustResolver
     */
    private $trustResolver;

    /**
     * @var TokenStorage
     */
    private $storage;

    /**
     * @var ContextEvent
     */
    private $event;

    public function __construct(Dispatcher $eventDispatcher, TrustResolver $trustResolver, TokenStorage $storage)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->trustResolver = $trustResolver;
        $this->storage = $storage;
    }

    public function handle(Request $request, \Closure $next)
    {
        $this->eventDispatcher->listen(ContextEvent::class, [$this, 'onSecurityEvent']);

        return $next($request);
    }

    public function terminate(SymfonyRequest $request, Response $response)
    {
        if (!$this->event) {
            return;
        }

        $token = $this->storage->getToken();
        $sessionKey = $this->event->sessionKey();

        if (!$token || $this->trustResolver->isAnonymous($token)) {
            $request->session()->forget($sessionKey);
        } else {
            $request->session()->put($sessionKey, serialize($token));
        }
    }

    public function onSecurityEvent(ContextEvent $event): void
    {
        if ($this->event) {
            throw new \RuntimeException('Only one session context can run per request');
        }

        $this->event = $event;
    }
}