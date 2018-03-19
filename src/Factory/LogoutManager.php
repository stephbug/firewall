<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory;

use Illuminate\Contracts\Foundation\Application;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;

class LogoutManager
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var array
     */
    private $services = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function setLogoutContext(array $payload): void
    {
        $this->services = $payload;
    }

    public function addHandler(string $handler, string $serviceKey): LogoutManager
    {
        if (!$this->hasService($serviceKey)) {
            throw InvalidArgument::reason(
                sprintf('Can not add logout handler with an unknown service key %s', $serviceKey)
            );
        }

        $this->services[$serviceKey] = [$handler];

        return $this;
    }

    final public function getHandlers(string $serviceKey): array
    {
        if (!$this->hasService($serviceKey)) {
            throw InvalidArgument::reason(
                sprintf('No logout service has been registered for service key %s', $serviceKey)
            );
        }

        $handlers = $this->services[$serviceKey];

        if (!$handlers) {
            throw InvalidArgument::reason(
                sprintf('No logout handler has been registered for service key %s', $serviceKey)
            );
        }

        return array_map(function (string $handler) {
            return $this->app->make($handler);
        }, $handlers);
    }

    public function hasService(string $serviceKey): bool
    {
        return isset($this->services[$serviceKey]);
    }
}