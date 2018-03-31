<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Bootstrap;

use StephBug\Firewall\Factory\Builder;
use StephBug\SecurityModel\Application\Http\Firewall\AccessControlFirewall;

class AccessControl extends AuthenticationRegistry
{
    const SERVICE_ALIAS = 'firewall.access_control';

    public function compose(Builder $builder, \Closure $make)
    {
        $this->app->bind(static::SERVICE_ALIAS, AccessControlFirewall::class);

        $this->registerFirewall(static::SERVICE_ALIAS, $builder);

        return $make($builder);
    }
}