<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Routing\Pipeline;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use StephBug\Firewall\Factory\Routing\Pipeline\Pipeline;
use StephBugTest\Firewall\Mock\SomeAuthenticationException;
use StephBugTest\Firewall\Mock\SomeDebugFirewall;
use StephBugTest\Firewall\Unit\TestCase;

class PipelineTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_response_when_security_exception_is_thrown(): void
    {
        $pipeline = new Pipeline($this->getApplication(), new SomeDebugFirewall());
        $request = new Request();
        $next = $this->next();
        $middleware = [$this->pipeRaiseSecurityException()];

        $response = $pipeline
            ->through($middleware)
            ->send($request)
            ->then(function () use ($request, $next) {
                return $next($request);
            });

        $this->assertEquals('some message', $response->getContent());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_raise_non_security_exception(): void
    {
        $this->expectExceptionMessage('another message');

        $pipeline = new Pipeline($this->getApplication(), new SomeDebugFirewall());
        $request = new Request();
        $next = $this->next();
        $middleware = [new class()
        {
            public function handle(Request $request, \Closure $next)
            {
                throw new \RuntimeException('another message');
            }
        }];

        $pipeline
            ->through($middleware)
            ->send($request)
            ->then(function () use ($request, $next) {
                return $next($request);
            });
    }

    private function next()
    {
        return function () {
        };
    }

    public function pipeRaiseSecurityException()
    {
        return new class()
        {
            public function handle(Request $request, \Closure $next)
            {
                throw new SomeAuthenticationException('some message');
            }
        };
    }

    private function getApplication(): Application
    {
        $app = new \Illuminate\Foundation\Application();
        $app::setInstance(new Container());

        return $app;
    }
}