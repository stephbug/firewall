<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Authentication;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Manager\LogoutManager;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\Firewall\Factory\Payload\PayloadService;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;

trait HasLogoutFactory
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var LogoutManager
     */
    protected $logoutManager;

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

        $this->addHandlersOnResolvingFirewall($payload->securityKey, $firewallId);

        return $factory->setFirewall($firewallId);
    }

    protected function addHandlersOnResolvingFirewall(SecurityKey $securityKey, string $firewallId): void
    {
        $this->app->resolving($firewallId, function ($firewall) use($securityKey) {
            if (!method_exists($firewall, 'addHandler')) {
                throw InvalidArgument::reason(
                    sprintf('Missing method "addHandler" on class %', get_class($firewall))
                );
            }

            foreach ($this->logoutManager->getResolvedHandlers($securityKey, $this->mirrorKey()) as $handler) {
                $firewall->addHandler($handler);
            }
        });
    }

    abstract protected function registerFirewall(PayloadService $payload): string;

    abstract public function mirrorKey(): string;
}