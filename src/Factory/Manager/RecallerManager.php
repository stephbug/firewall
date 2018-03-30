<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Manager;

use Illuminate\Contracts\Cookie\QueueingFactory;
use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Payload\PayloadRecaller;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;
use StephBug\SecurityModel\Guard\Service\Recaller\CookieSecurity;
use StephBug\SecurityModel\Guard\Service\Recaller\SimpleRecallerService;

class RecallerManager
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var array
     */
    private $services = [];

    /**
     * @var array [$serviceKey => callable]
     */
    private $callback = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function make(PayloadRecaller $payload): string
    {
        if ($this->hasService($payload->serviceKey)) {
            return $this->getService($payload->serviceKey);
        }

        return $this->services[$payload->serviceKey] = $this->createService($payload);
    }

    protected function createService(PayloadRecaller $payload): string
    {
        $recallerId = isset($this->callback[$payload->serviceKey])
            ? ($this->callback[$payload->serviceKey])($this->app)
            : ($this->registerSimpleService($payload))($this->app);

        $this->setRecallerOnResolvingListener($recallerId, $payload->firewallId);

        return $recallerId;
    }

    public function hasService(string $serviceKey)
    {
        return isset($this->services[$serviceKey]);
    }

    public function getService(string $serviceKey): string
    {
        if ($this->hasService($serviceKey)) {
            return $this->services[$serviceKey];
        }

        throw InvalidArgument::reason(
            sprintf('No recaller service has been registered for service key %s', $serviceKey)
        );
    }

    public function extend(string $serviceKey, callable $callback): RecallerManager
    {
        $this->callback[$serviceKey] = $callback;

        return $this;
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