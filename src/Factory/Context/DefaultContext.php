<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Context;

use StephBug\Firewall\Factory\Contracts\FirewallContext;

class DefaultContext implements FirewallContext
{
    use HasContext;

    protected $original = [
        'anonymous' => false,
        'stateless' => false,
        'securityKey' => 'default_security_key',
        'recallerKey' => 'default_recaller_key',
        'anonymousKey' => 'default_anonymous_key',
        'userProviderId' => 'eloquent',
        'entrypointId' => 'default_entry_point_id',
        'unauthorizedId' => 'default_unauthorized_id',
        'logout' => [],
        'recaller' => [],
        'allowToSwitch' => true,
    ];

    /**
     * @var array
     */
    protected $attributes;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes ?? $this->original;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}