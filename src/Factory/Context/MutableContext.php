<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Context;

use StephBug\Firewall\Factory\Contracts\MutableContext as BaseMutable;
use StephBug\Firewall\Factory\Contracts\ToImmutableContext;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;

final class MutableContext extends DefaultContext implements BaseMutable, ToImmutableContext
{
    use HasMutableContext;

    public function setAttribute(string $name, $value): void
    {
        if (!isset($this->attributes[$name])) {
            throw InvalidArgument::reason(
                sprintf('Unknown attribute name %s for context class %s', $name, get_class($this))
            );
        }

        $this->attributes[$name] = $value;
    }

    public function toImmutable(): array
    {
        return $this->getAttributes();
    }
}