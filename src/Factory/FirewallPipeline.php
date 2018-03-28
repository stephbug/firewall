<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\SecurityException;

class FirewallPipeline
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var string
     */
    private $firewallName;

    /**
     * @var array
     */
    private $middleware;

    public function __construct(string $firewallName, array $middleware, Application $app)
    {
        $this->app = $app;
        $this->firewallName = $firewallName;
        $this->middleware = $middleware;
    }

    public function handle(Request $request, \Closure $next)
    {
        try {
            foreach ($this->middleware as $middleware) {
                return $this->app->make($middleware)->handle($request, $next);
            }

        } catch (SecurityException $exception) {
            dd($exception);
        }
    }
}