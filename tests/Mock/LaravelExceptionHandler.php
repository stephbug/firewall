<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Mock;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use StephBug\Firewall\Application\Exception\DebugFirewall;
use Symfony\Component\HttpFoundation\Response;

class LaravelExceptionHandler implements ExceptionHandler
{
    private $debug;

    public function setFirewallHandler(DebugFirewall $debug)
    {
        $this->debug = $debug;
    }

    public function hasDebug(): bool
    {
        return $this->debug instanceof DebugFirewall;
    }

    public function report(Exception $e): void
    {
    }

    public function render($request, Exception $e): Response
    {
        return new Response('foo');
    }

    public function renderForConsole($output, Exception $e): void
    {
    }
}