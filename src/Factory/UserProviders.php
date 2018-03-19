<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory;

use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;

class UserProviders
{
    /**
     * @var array
     */
    private $userProviders;

    public function __construct(array $userProviders)
    {
        $this->userProviders = $userProviders;
    }

    public function get(FirewallContext $context, string $provider = null): string
    {
        $id = $provider ?? $context->userProviderId();

        if (!array_key_exists($id, $this->userProviders)) {
            throw InvalidArgument::reason(
                sprintf('User provider with id %s does not exist', $id)
            );
        }

        return $this->userProviders[$id];
    }

    public function toArray(): array
    {
        return array_flatten($this->userProviders);
    }
}