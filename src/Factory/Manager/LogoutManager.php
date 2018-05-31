<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Manager;

use Illuminate\Contracts\Foundation\Application;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;

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

    public function addLogoutContext(SecurityKey $securityKey, array $payload): void
    {
        $key = $securityKey->value() . '.' . key($payload);
        $values = array_values($payload);

        $this->services = array_add($this->services, $key, $values);
    }

    public function addHandler(SecurityKey $securityKey, string $serviceKey, string $handler): LogoutManager
    {
        if (!$this->hasService($securityKey, $serviceKey)) {
            throw InvalidArgument::reason(
                sprintf('Can not add logout handler with an unknown service key %s and security key %s',
                    $serviceKey, $securityKey->value())
            );
        }

        $this->services[$securityKey->value()][$serviceKey] = [$handler];

        return $this;
    }

    public function getResolvedHandlers(SecurityKey $securityKey, string $serviceKey): array
    {
        if (!$this->hasService($securityKey, $serviceKey)) {
            throw InvalidArgument::reason(
                sprintf('No logout service has been registered for service key %s and security key %s',
                    $serviceKey, $securityKey->value())
            );
        }

        $handlers = array_filter($this->services[$securityKey->value()][$serviceKey]);

        if (!is_array($handlers) || empty($handlers)) {
            throw InvalidArgument::reason(
                sprintf('No logout handler has been registered for service key %s', $serviceKey)
            );
        }

        return $this->resolveHandlers($handlers);
    }

    public function hasService(SecurityKey $securityKey, string $serviceKey): bool
    {
        $service = $this->services[$securityKey->value()][$serviceKey] ?? null;

        return null !== $service;
    }

    private function resolveHandlers(array $handlers): array
    {
        return array_map(function (string $handler) {
            return $this->app->make($handler);
        }, $handlers);
    }
}