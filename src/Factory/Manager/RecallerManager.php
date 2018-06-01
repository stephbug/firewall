<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Manager;

use Illuminate\Contracts\Cookie\QueueingFactory;
use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Payload\PayloadRecaller;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Guard\Service\Recaller\Encoder\CookieSecurity;
use StephBug\SecurityModel\Guard\Service\Recaller\SimpleRecallerService;

class RecallerManager
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $services = [];

    /**
     * @var array [$serviceKey => callable]
     */
    protected $callback = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function make(PayloadRecaller $payload): string
    {
        $securityKey = $payload->service->securityKey;
        $serviceKey = $payload->serviceKey;

        if ($this->hasService($securityKey, $serviceKey)) {
            return $this->getService($securityKey, $serviceKey);
        }

        return $this->services[$securityKey->value()][$serviceKey] = $this->createService($payload);
    }

    protected function createService(PayloadRecaller $payload): string
    {
        $recallerId = ($this->determineCallbackService($payload))($this->app);

        $this->setRecallerOnResolvingListener($recallerId, $payload->firewallId);

        return $recallerId;
    }

    public function hasService(SecurityKey $securityKey, string $serviceKey): bool
    {
        return null !== ($this->services[$securityKey->value()][$serviceKey] ?? null);
    }

    public function getService(SecurityKey $securityKey, string $serviceKey): string
    {
        if ($this->hasService($securityKey, $serviceKey)) {
            return $this->services[$securityKey->value()][$serviceKey];
        }

        throw InvalidArgument::reason(
            sprintf('No recaller service has been registered for service key %s and security key %s',
                $serviceKey, $securityKey->value())
        );
    }

    public function extend(string $securityKey, string $serviceKey, callable $callback): RecallerManager
    {
        $this->callback[$securityKey][$serviceKey] = $callback;

        return $this;
    }

    protected function determineCallbackService(PayloadRecaller $payload): callable
    {
        $securityKey = $payload->service->securityKey->value();
        $serviceKey = $payload->serviceKey;

        if (isset($this->callback[$securityKey][$serviceKey])) {
            return $this->callback[$securityKey][$serviceKey];
        }

        return $this->registerSimpleService($payload);
    }

    protected function registerSimpleService(PayloadRecaller $payload): callable
    {
        return function (Application $app) use ($payload) {
            $id = 'firewall.simple_recaller_service.' . $payload->firewallId;

            $app->bind($id, function (Application $app) use ($payload) {
                $recallerKey = $payload->service->context->recaller($payload->serviceKey);

                return new SimpleRecallerService(
                    $app->make(QueueingFactory::class),
                    new CookieSecurity($recallerKey),
                    $app->make($payload->service->userProviderId),
                    $recallerKey,
                    $payload->service->securityKey
                );
            });

            return $id;
        };
    }

    protected function setRecallerOnResolvingListener(string $recallerId, string $firewallId): void
    {
        $this->app->resolving($firewallId, function ($firewall, Application $app) use ($recallerId) {
            if (!method_exists($firewall, 'setRecaller')) {
                throw InvalidArgument::reason(
                    sprintf('Missing "setRecaller" method on firewall class %s', get_class($firewall))
                );
            }

            $firewall->setRecaller($app->make($recallerId));
        });
    }
}