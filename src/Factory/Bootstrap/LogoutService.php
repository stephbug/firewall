<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Contracts\FirewallRegistry;
use StephBug\Firewall\Factory\LogoutHandler;

class LogoutService implements FirewallRegistry
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var LogoutHandler
     */
    private $logoutHandler;

    public function __construct(Application $app, LogoutHandler $logoutHandler)
    {
        $this->app = $app;
        $this->logoutHandler = $logoutHandler;
    }

    public function compose(Builder $builder, \Closure $make)
    {
        if ($this->logoutHandler->hasServices()) {

        }

        return $make($builder);
    }
}