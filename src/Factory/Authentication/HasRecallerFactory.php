<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Authentication;

use StephBug\Firewall\Factory\Manager\RecallerManager;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\Firewall\Factory\Payload\PayloadService;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;
use StephBug\SecurityModel\Application\Values\Security\RecallerKey;

trait HasRecallerFactory
{
    public function create(PayloadService $payload): PayloadFactory
    {
        $recallerKey = $payload->context->recaller($this->mirrorKey());

        if (!$recallerKey) {
            throw InvalidArgument::reason(sprintf(
                'Missing recaller key for service key %s', $this->mirrorKey()
            ));
        }

        if (!$this->getRecallerManager()->hasService($payload->securityKey, $this->mirrorKey())) {
            throw InvalidArgument::reason(sprintf(
                'No recaller service has been registered for service key %s', $this->mirrorKey()
            ));
        }

        $recallerServiceId = $this->getRecallerManager()->getService($payload->securityKey, $this->mirrorKey());

        return (new PayloadFactory())
            ->setFirewall($this->registerFirewall($payload, $recallerServiceId))
            ->setProvider($this->registerProvider($payload, $recallerKey));
    }

    abstract public function mirrorKey(): string;

    abstract protected function registerFirewall(PayloadService $payload, string $recallerServiceId): string;

    abstract protected function registerProvider(PayloadService $payload, RecallerKey $recallerKey): string;

    abstract protected function getRecallerManager(): RecallerManager;
}