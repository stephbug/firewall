<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Mock;

use Illuminate\Http\Request;

class SomeFirewallPipe
{
    public function handle(Request $request, \Closure $next)
    {
        return $next($request);
    }
}