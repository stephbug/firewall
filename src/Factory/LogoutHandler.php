<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory;

class LogoutHandler
{
    private $services = [];

    public function hasServices(): bool
    {
        return !empty($this->services);
    }
}