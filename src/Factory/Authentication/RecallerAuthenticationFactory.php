<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Authentication;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Contracts\AuthenticationServiceFactory;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\Firewall\Factory\Payload\PayloadService;
use StephBug\Firewall\Factory\RecallerManager;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;
use StephBug\SecurityModel\Application\Values\RecallerKey;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

abstract class RecallerAuthenticationFactory implements AuthenticationServiceFactory
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var RecallerManager
     */
    private $recallerManager;

    public function __construct(Application $app, RecallerManager $recallerManager)
    {
        $this->app = $app;
        $this->recallerManager = $recallerManager;
    }

    public function create(PayloadService $payload): PayloadFactory
    {
        $recallerKey = $payload->context->recaller($this->mirrorKey());

        if (!$recallerKey) {
            throw InvalidArgument::reason(sprintf(
                'Missing recaller key for service key %s', $this->mirrorKey()
            ));
        }

        if (!$this->recallerManager->hasService($this->mirrorKey())) {
            throw InvalidArgument::reason(sprintf(
                'No recaller service has been registered for service key %s', $this->mirrorKey()
            ));
        }

        $recallerServiceId = $this->recallerManager->getService($this->mirrorKey());

        return (new PayloadFactory())
            ->setFirewall($this->registerFirewall($payload, $recallerServiceId))
            ->setProvider($this->registerProvider($payload, $recallerKey));
    }

    public function position(): string
    {
        return 'remember_me';
    }

    abstract public function mirrorKey(): string;

    abstract protected function registerFirewall(PayloadService $payload, string $recallerServiceId): string;

    abstract protected function registerProvider(PayloadService $payload, RecallerKey $recallerKey): string;


    public function matcher(): ?RequestMatcherInterface
    {
        return null;
    }

    public function userProviderKey(): ?string
    {
        return null;
    }
}