<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Application\Exception;

use Illuminate\Http\Request;
use StephBug\Firewall\Application\Exception\AuthorizationHandler;
use StephBug\Firewall\Application\Exception\ContextualHandler;
use StephBug\SecurityModel\Application\Exception\InsufficientAuthentication;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorage;
use StephBug\SecurityModel\Guard\Authentication\TrustResolver;
use StephBugTest\Firewall\Mock\FirewallIdentifier;
use StephBugTest\Firewall\Mock\FirewallToken;
use StephBugTest\Firewall\Mock\SecurityTestKey;
use StephBugTest\Firewall\Mock\SomeAuthorizationException;
use StephBugTest\Firewall\Mock\SomeDeniedHandler;
use StephBugTest\Firewall\Mock\SomeEntrypoint;
use StephBugTest\Firewall\Unit\TestCase;

class AuthorizationHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function it_handle_authorization_exception(): void
    {
        $ins = $this->getAuthorizationHandlerInstance();
        $contextual = $this->getMockBuilder(ContextualHandler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $contextual->expects($this->once())->method('deniedHandler')->willReturn(
            $denied = new SomeDeniedHandler('bar')
        );
        $exc = new SomeAuthorizationException();
        $token = new FirewallToken(new FirewallIdentifier());

        $this->storage->expects($this->once())->method('getToken')->willReturn($token);
        $this->trustResolver->expects($this->once())->method('isFullyAuthenticated')->willReturn(true);

        $response = $ins->handle($exc, new Request(), $contextual);

        $this->assertEquals('bar', $response->getContent());
    }

    /**
     * @test
     * @expectedException \StephBugTest\Firewall\Mock\SomeAuthorizationException
     */
    public function it_raise_exception_caught_when_denied_handler_has_not_been_defined(): void
    {
        $ins = $this->getAuthorizationHandlerInstance();
        $contextual = $this->getMockBuilder(ContextualHandler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $contextual->expects($this->once())->method('deniedHandler')->willReturn(null);
        $exc = new SomeAuthorizationException();
        $token = new FirewallToken(new FirewallIdentifier());

        $this->storage->expects($this->once())->method('getToken')->willReturn($token);
        $this->trustResolver->expects($this->once())->method('isFullyAuthenticated')->willReturn(true);

        $ins->handle($exc, new Request(), $contextual);
    }

    /**
     * @test
     */
    public function it_transform_an_authorization_exception_into_an_authentication_exception(): void
    {
        $ins = $this->getAuthorizationHandlerInstance();
        $contextual = $this->getContextualHandlerInstance();

        $exc = new SomeAuthorizationException();
        $token = new FirewallToken(new FirewallIdentifier());

        $this->storage->expects($this->once())->method('getToken')->willReturn($token);
        $this->trustResolver->expects($this->once())->method('isFullyAuthenticated')->willReturn(false);

        $response = $ins->handle($exc, new Request(), $contextual);

        $this->assertEquals('Full authentication is required to access this resource.', $response->getContent());
        $this->assertInstanceOf(InsufficientAuthentication::class, $contextual->entrypoint()->getException());
        $this->assertEquals($exc, $contextual->entrypoint()->getException()->getPrevious());
    }

    private function getContextualHandlerInstance(): ContextualHandler
    {
        return new ContextualHandler(
            $this->storage,
            new SecurityTestKey('foo'),
            $this->trustResolver,
            false,
            new SomeEntrypoint() // no message as we expect message from exception
        );
    }

    private function getAuthorizationHandlerInstance(): AuthorizationHandler
    {
        return new AuthorizationHandler(
            $this->storage,
            $this->trustResolver
        );
    }

    private $storage;
    private $trustResolver;
    protected function setUp()
    {
        $this->storage = $this->getMockForAbstractClass(TokenStorage::class);
        $this->trustResolver = $this->getMockForAbstractClass(TrustResolver::class);
    }
}