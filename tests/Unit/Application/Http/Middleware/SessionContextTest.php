<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Application\Http\Middleware;

use Illuminate\Contracts\Session\Session;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use StephBug\Firewall\Application\Http\Middleware\SessionContext;
use StephBug\SecurityModel\Application\Http\Event\ContextEvent;
use StephBug\SecurityModel\Application\Values\Identifier\AnonymousIdentifier;
use StephBug\SecurityModel\Application\Values\Security\AnonymousKey;
use StephBug\SecurityModel\Application\Values\Security\FirewallKey;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\Token\AnonymousToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorage;
use StephBug\SecurityModel\Guard\Authentication\TrustResolver;
use StephBugTest\Firewall\Mock\FirewallIdentifier;
use StephBugTest\Firewall\Mock\FirewallToken;
use StephBugTest\Firewall\Unit\TestCase;
use Symfony\Component\HttpFoundation\Response;

class SessionContextTest extends TestCase
{
    /**
     * @test
     */
    public function it_listen_to_context_event(): void
    {
        $context = $this->getSessionContextInstance();

        $ref = new \ReflectionObject($context);
        $eventable = $ref->getProperty('event');
        $eventable->setAccessible(true);

        $this->assertNull($eventable->getValue($context));
        $context->handle(new Request(), function () {
        });

        $this->dispatcher->dispatch($contextEvent = new ContextEvent(new FirewallKey('foo')));

        $this->assertEquals($contextEvent, $eventable->getValue($context));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_accept_only_one_context_event_per_request(): void
    {
        $this->expectExceptionMessage('Only one session context can run per request');

        $context = $this->getSessionContextInstance();
        $context->handle(new Request(), $this->next());

        $this->dispatcher->dispatch(new ContextEvent(new FirewallKey('foo')));
        $this->dispatcher->dispatch(new ContextEvent(new FirewallKey('bar')));
    }

    /**
     * @test
     */
    public function it_only_process_if_context_event_has_been_dispatched(): void
    {
        $context = $this->getSessionContextInstance();

        $ref = new \ReflectionObject($context);
        $eventable = $ref->getProperty('event');
        $eventable->setAccessible(true);

        $this->assertNull($eventable->getValue($context));
        $this->tokenStorage->expects($this->never())->method('getToken');

        $context->handle(new Request(), $this->next());
    }

    /**
     * @test
     */
    public function it_erase_session_if_token_storage_is_empty(): void
    {
        $context = $this->getSessionContextInstance();

        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn(null);

        $key = $this->getMockBuilder(SecurityKey::class)
            ->disableOriginalConstructor()
            ->getMock();
        $key->expects($this->once())->method('value')->willReturn('foo');
        $event = new ContextEvent($key);

        $request = new Request();
        $session = $this->getMockForAbstractClass(Session::class);
        $session->expects($this->once())->method('forget');
        $request->setLaravelSession($session);


        $context->handle($request, $this->next());
        $this->dispatcher->dispatch($event);

        $context->terminate($request, new Response('foo_bar'));
    }

    /**
     * @test
     */
    public function it_erase_session_if_token_is_an_instanceof_anonymous_token(): void
    {
        $context = $this->getSessionContextInstance();

        $key = $this->getMockBuilder(SecurityKey::class)
            ->disableOriginalConstructor()
            ->getMock();
        $key->expects($this->once())->method('value')->willReturn('foo');
        $event = new ContextEvent($key);

        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn(
            new AnonymousToken(new AnonymousIdentifier(), new AnonymousKey('foo_foo'))
        );
        $this->trustResolver->expects($this->once())->method('isAnonymous')->willReturn(true);

        $request = new Request();
        $session = $this->getMockForAbstractClass(Session::class);
        $session->expects($this->once())->method('forget');
        $request->setLaravelSession($session);


        $context->handle($request, $this->next());
        $this->dispatcher->dispatch($event);

        $context->terminate($request, new Response('foo_bar'));
    }

    /**
     * @test
     */
    public function it_store_in_session_any_non_anonymous_token(): void
    {
        $context = $this->getSessionContextInstance();

        $key = $this->getMockBuilder(SecurityKey::class)
            ->disableOriginalConstructor()
            ->getMock();
        $key->expects($this->once())->method('value')->willReturn('foo');
        $event = new ContextEvent($key);

        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn(
            new FirewallToken(new FirewallIdentifier())
        );
        $this->trustResolver->expects($this->once())->method('isAnonymous')->willReturn(false);

        $request = new Request();
        $session = $this->getMockForAbstractClass(Session::class);
        $session->expects($this->once())->method('put');
        $request->setLaravelSession($session);


        $context->handle($request, $this->next());
        $this->dispatcher->dispatch($event);

        $context->terminate($request, new Response('foo_bar'));
    }

    private function getSessionContextInstance(): SessionContext
    {
        return new SessionContext($this->dispatcher, $this->trustResolver, $this->tokenStorage);
    }

    private function next()
    {
        return function () {
        };
    }

    private $dispatcher;
    private $tokenStorage;
    private $trustResolver;

    public function setUp()
    {
        $this->dispatcher = new Dispatcher();
        $this->tokenStorage = $this->getMockForAbstractClass(TokenStorage::class);
        $this->trustResolver = $this->getMockForAbstractClass(TrustResolver::class);
    }
}