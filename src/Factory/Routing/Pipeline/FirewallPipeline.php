<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Routing\Pipeline;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use StephBug\Firewall\Application\Exception\DebugFirewall;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;

class FirewallPipeline
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var array
     */
    private $middleware;

    /**
     * @var string
     */
    private $firewallName;

    public function __construct(Application $app, array $middleware, string $firewallName)
    {
        $this->app = $app;
        $this->middleware = $middleware;
        $this->firewallName = $firewallName;
    }

    public function handle(Request $request, \Closure $next)
    {
        $middleware = $this->middleware;

        $exception = $this->determineDebugFirewallHandler(array_pop($middleware));

        return (new Pipeline($this->app, $exception))
            ->through($middleware)
            ->send($request)
            ->then(function () use ($request, $next) {
                return $next($request);
            });
    }

    protected function determineDebugFirewallHandler(string $exceptionId): DebugFirewall
    {
        $exception = $this->app->make($exceptionId);

        if (!$exception instanceof DebugFirewall) {
            throw InvalidArgument::reason(
                sprintf('Last middleware of firewall %s must implement %s interface',
                    $this->firewallName, DebugFirewall::class)
            );
        }

        $this->setExceptionHandler($exception);

        return $exception;
    }

    protected function setExceptionHandler(DebugFirewall $exceptionHandler): void
    {
        if ($this->app->bound(ExceptionHandler::class)) {
            $this->app->make(ExceptionHandler::class)->setFirewallHandler($exceptionHandler);
        }
    }
}