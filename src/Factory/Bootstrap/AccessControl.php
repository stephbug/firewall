<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Contracts\FirewallRegistry;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\SecurityModel\Application\Http\Firewall\AccessControlFirewall;

class AccessControl implements FirewallRegistry
{
    const SERVICE_ALIAS = 'firewall.access_control';

    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function compose(Builder $builder, \Closure $make)
    {
        $this->app->bind(static::SERVICE_ALIAS, AccessControlFirewall::class);

        $builder((new PayloadFactory())->setFirewall(static::SERVICE_ALIAS));

        return $make($builder);
    }
}