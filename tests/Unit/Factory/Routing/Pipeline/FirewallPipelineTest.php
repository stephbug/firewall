<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Routing\Pipeline;

use Illuminate\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use StephBug\Firewall\Factory\Routing\Pipeline\FirewallPipeline;
use StephBugTest\Firewall\Mock\LaravelExceptionHandler;
use StephBugTest\Firewall\Mock\SomeDebugFirewall;
use StephBugTest\Firewall\Mock\SomeFirewallPipe;
use StephBugTest\Firewall\Unit\TestCase;

class FirewallPipelineTest extends TestCase
{
    /**
     * @test
     */
    public function it_handle_security_middleware(): void
    {
        $app = $this->getApplication();
        $middleware = ['some_pipe', 'last_pipe', 'debug_handler'];

        $app->bind($middleware[0], SomeFirewallPipe::class);
        $app->bind($middleware[1], $this->returnResponseForLastPipe());
        $app->bind($middleware[2], SomeDebugFirewall::class);

        $pipeline = new FirewallPipeline($app, $middleware, 'foobar');
        $request = new Request();

        $response = $pipeline->handle($request, $this->next());

        $this->assertEquals('foo', $response->getContent());
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_when_last_middleware_does_not_implement_debug_firewall_interface(): void
    {
        $app = $this->getApplication();
        $middleware = ['some_pipe'];

        $app->bind($middleware[0], SomeFirewallPipe::class);

        $pipeline = new FirewallPipeline($app, $middleware, 'foobar');
        $request = new Request();

        $pipeline->handle($request, $this->next());
    }

    /**
     * @test
     */
    public function it_set_debug_firewall_on_laravel_exception_handler(): void
    {
        $app = $this->getApplication();
        $middleware = ['some_pipe', 'last_pipe', 'debug_handler'];

        $app->bind($middleware[0], SomeFirewallPipe::class);
        $app->bind($middleware[1], $this->returnResponseForLastPipe());
        $app->bind($middleware[2], SomeDebugFirewall::class);

        $handler = new LaravelExceptionHandler();
        $app->instance(ExceptionHandler::class, $handler);

        $this->assertFalse($handler->hasDebug());

        $pipeline = new FirewallPipeline($app, $middleware, 'foobar');
        $request = new Request();

        $response = $pipeline->handle($request, $this->next());

        $this->assertEquals('foo', $response->getContent());
        $this->assertTrue($handler->hasDebug());
    }

    private function returnResponseForLastPipe()
    {
        return function () {
            return new class()
            {
                public function handle(Request $request, \Closure $next)
                {
                    return new Response('foo');
                }
            };
        };
    }

    private function next()
    {
        return function () {
        };
    }

    private function getApplication(): Application
    {
        $app = new \Illuminate\Foundation\Application();
        $app::setInstance(new Container());

        return $app;
    }
}