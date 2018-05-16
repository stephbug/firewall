<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Authentication;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Contracts\AuthenticationServiceFactory;
use StephBug\Firewall\Factory\Manager\LogoutManager;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\Firewall\Factory\Payload\PayloadService;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;

abstract class LogoutAuthenticationFactory implements AuthenticationServiceFactory
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var LogoutManager
     */
    private $logoutManager;

    public function __construct(Application $app, LogoutManager $logoutManager)
    {
        $this->app = $app;
        $this->logoutManager = $logoutManager;
    }

    public function create(PayloadService $payload): PayloadFactory
    {
        $factory = new PayloadFactory();

        if (!$payload->context->logoutByKey($this->mirrorKey())) {
            return $factory;
        }

        $firewallId = $this->registerFirewall($payload);

        $this->addHandlersOnResolvingFirewall($firewallId);

        return $factory->setFirewall($firewallId);
    }

    protected function addHandlersOnResolvingFirewall(string $firewallId): void
    {
        $this->app->resolving($firewallId, function ($firewall) {
            if (!method_exists($firewall, 'addHandler')) {
                throw InvalidArgument::reason(
                    sprintf('Missing method "addHandler" on class %', get_class($firewall))
                );
            }

            foreach ($this->logoutManager->getResolvedHandlers($this->mirrorKey()) as $handler) {
                $firewall->addHandler($handler);
            }
        });
    }

    abstract protected function registerFirewall(PayloadService $payload): string;

    abstract public function mirrorKey(): string;

    public function userProviderKey(): ?string
    {
        return null;
    }
}