<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Contracts;

use StephBug\Firewall\Factory\Builder;

interface FirewallRegistry
{
    public function compose(Builder $builder, \Closure $make);
}