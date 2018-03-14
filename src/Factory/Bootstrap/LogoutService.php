<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Contracts\FirewallRegistry;
use StephBug\Firewall\Factory\LogoutManager;

class LogoutService implements FirewallRegistry
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var LogoutManager
     */
    private $logoutManager;

    public function __construct(Application $app, LogoutManager $logoutManager)
    {
        $this->app = $app;
        $this->logoutManager = $logoutManager;
    }

    public function compose(Builder $builder, \Closure $make)
    {
        if (!empty($payload = $builder->context()->logout())) {
            $this->logoutManager->setLogoutContext($payload);
        }

        return $make($builder);
    }
}