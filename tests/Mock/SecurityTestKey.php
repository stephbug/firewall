<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Mock;

use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;

class SecurityTestKey extends SecurityKey
{
    public function sameValueAs(SecurityValue $aValue): bool
    {
        return $aValue instanceof $this && $this->key === $aValue->value();
    }
}