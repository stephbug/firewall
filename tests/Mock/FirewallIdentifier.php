<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Mock;

use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;

class FirewallIdentifier implements SecurityIdentifier, UserToken
{
    public function identify(): string
    {
        return 'firewall_identifier';
    }

    public function sameValueAs(SecurityValue $aValue): bool
    {
        return true;
    }
}