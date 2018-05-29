<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Mock;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use StephBug\Firewall\Application\Exception\DebugFirewall;
use StephBug\SecurityModel\Application\Exception\SecurityException;

class SomeDebugFirewall implements DebugFirewall
{
    public function handle(Request $request, SecurityException $exception): Response
    {
        return new Response($exception->getMessage() ?? 'no exception message provided');
    }
}