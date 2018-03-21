<?php

declare(strict_types=1);

namespace StephBug\Firewall\Application\Http\Middleware;

use Illuminate\Http\Request;
use StephBug\Firewall\Factory\Strategy\FirewallStrategy;

class Firewall
{
    /**
     * @var FirewallStrategy
     */
    private $strategy;

    public function __construct(FirewallStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function handle(Request $request, \Closure $next)
    {
        $this->strategy->delegateHandling($request);

        return $next($request);
    }
}