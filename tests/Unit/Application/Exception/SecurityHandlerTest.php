<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Application\Exception;

use Illuminate\Http\Request;
use StephBug\Firewall\Application\Exception\AuthenticationHandler;
use StephBug\Firewall\Application\Exception\AuthorizationHandler;
use StephBug\Firewall\Application\Exception\ContextualHandler;
use StephBug\Firewall\Application\Exception\SecurityHandler;
use StephBug\SecurityModel\Application\Exception\SecurityException;
use StephBugTest\Firewall\Mock\SomeAuthenticationException;
use StephBugTest\Firewall\Mock\SomeAuthorizationException;
use StephBugTest\Firewall\Unit\TestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function it_handle_authentication_exception(): void
    {
        $this->authentication->expects($this->once())->method('handle');
        $this->authorization->expects($this->never())->method('handle');

        $ins = $this->getSecurityHandlerInstance();
        $response = $ins->handle(new Request(), new SomeAuthenticationException());

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @test
     */
    public function it_handle_authorization_exception(): void
    {
        $this->authorization->expects($this->once())->method('handle');
        $this->authentication->expects($this->never())->method('handle');

        $ins = $this->getSecurityHandlerInstance();
        $response = $ins->handle(new Request(), new SomeAuthorizationException());

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @test
     */
    public function it_raise_caught_exception_which_is_not_an_auth_exception(): void
    {
        $this->authorization->expects($this->never())->method('handle');
        $this->authentication->expects($this->never())->method('handle');

        $ins = $this->getSecurityHandlerInstance();
        $exc = new class() extends \RuntimeException implements SecurityException {};

        $this->expectExceptionObject($exc);

        $ins->handle(new Request(), $exc);
    }

    private function getSecurityHandlerInstance(): SecurityHandler
    {
        return new SecurityHandler(
            $this->contextual,
            $this->authentication,
            $this->authorization
        );
    }

    private $authentication;
    private $authorization;
    private $contextual;

    public function setUp(): void
    {
        $this->authentication = $this->getMockBuilder(AuthenticationHandler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->authorization = $this->getMockBuilder(AuthorizationHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contextual = $this->getMockBuilder(ContextualHandler::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}