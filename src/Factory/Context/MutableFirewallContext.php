<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Context;

use StephBug\Firewall\Factory\Contracts\MutableFirewallContext as BaseMutable;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;

class MutableFirewallContext extends DefaultFirewallContext implements BaseMutable
{
    use HasMutableContext;

    protected function setAttribute(string $name, $value): void
    {
        if (!isset($this->attributes[$name])) {
            throw InvalidArgument::reason(
                sprintf('Unknown attribute name %s for context class %s', $name, get_class($this))
            );
        }

        $this->attributes[$name] = $value;
    }
}