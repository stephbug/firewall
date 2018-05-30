<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Context;

use StephBug\Firewall\Factory\Contracts\FirewallContext;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;

final class ImmutableContext implements FirewallContext
{
    use HasContext;

    /**
     * @var array
     */
    private $attributes;

    public function __construct(array $attributes)
    {
        if (empty($attributes)) {
            throw InvalidArgument::reason(
                sprintf('attributes for class %s can not be empty',
                    ImmutableContext::class
                )
            );
        }

        $this->attributes = $attributes;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}