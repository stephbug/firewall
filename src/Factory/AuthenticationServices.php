<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use StephBug\Firewall\Factory\Contracts\AuthenticationServiceFactory;

class AuthenticationServices
{
    /**
     * @var FirewallMap
     */
    private $firewallMap;

    public function __construct(FirewallMap $firewallMap)
    {

        $this->firewallMap = $firewallMap;
    }

    public function map(Request $request): Collection
    {

    }
}