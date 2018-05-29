<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Application\Http\Middleware;

use Illuminate\Http\Request;
use StephBug\Firewall\Application\Http\Middleware\Authorization;
use StephBug\SecurityModel\Guard\Authorizer;
use StephBug\SecurityModel\Role\Exception\AuthorizationDenied;
use StephBugTest\Firewall\Unit\TestCase;

class AuthorizationTest extends TestCase
{
    /**
     * @test
     */
    public function it_grant_access_with_attributes(): void
    {
        $m = $this->getMockBuilder(Authorizer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $middleware = new Authorization($m);

        $m->expects($this->once())->method('requireGranted')->willReturn(true);

        $middleware->handle(new Request(), $this->next(), ['foo']);
    }

    /**
     * @test
     */
    public function it_does_not_handle_authorization_with_no_attributes(): void
    {
        $m = $this->getMockBuilder(Authorizer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $middleware = new Authorization($m);

        $m->expects($this->never())->method('requireGranted');

        $middleware->handle(new Request(), $this->next());
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Role\Exception\AuthorizationDenied
     */
    public function it_raise_exception_on_authorization_denied(): void
    {
        $m = $this->getMockBuilder(Authorizer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $middleware = new Authorization($m);

        $m->expects($this->once())->method('requireGranted')->willThrowException(
            new AuthorizationDenied('foo')
        );

        $middleware->handle(new Request(), $this->next(), ['bar']);
    }

    private function next(): callable
    {
        return function () {};
    }
}