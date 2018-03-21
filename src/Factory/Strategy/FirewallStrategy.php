<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Strategy;

use Illuminate\Http\Request;

interface FirewallStrategy
{
    public function delegateHandling(Request $request): void;
}