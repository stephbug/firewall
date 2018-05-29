<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Mock;

use StephBug\SecurityModel\Application\Values\Contract\Credentials;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Application\Values\User\EmptyCredentials;
use StephBug\SecurityModel\Guard\Authentication\Token\Token;

class FirewallToken extends Token
{
    public function __construct(UserToken $user,array $roles = [])
    {
        parent::__construct($roles);
        $this->setUser($user);

        $this->hasRoles() and $this->setAuthenticated(true);
    }

    public function getCredentials(): Credentials
    {
        return new EmptyCredentials();
    }

    public function getSecurityKey(): SecurityKey
    {
        return new SecurityTestKey('foo');
    }
}