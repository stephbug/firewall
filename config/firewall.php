<?php

return [

    'services' => [

        'firewall_name' => [

            'context' => 'firewall context',

            /**
             * [service key, service factory|middleware, request matcher]
             */
            'map' => [],

        ],
    ],

    'bootstraps' => [

        \StephBug\Firewall\Factory\Bootstrap\AuthenticationProvider::class,
        \StephBug\Firewall\Factory\Bootstrap\EntrypointRegistry::class,
        \StephBug\Firewall\Factory\Bootstrap\LogoutService::class,
        \StephBug\Firewall\Factory\Bootstrap\SerializationContext::class,
        \StephBug\Firewall\Factory\Bootstrap\AuthenticationService::class,
        \StephBug\Firewall\Factory\Bootstrap\AnonymousRequest::class,
        \StephBug\Firewall\Factory\Bootstrap\ImpersonateUser::class,
        \StephBug\Firewall\Factory\Bootstrap\AccessControl::class,
        \StephBug\Firewall\Factory\Bootstrap\FirewallExceptionHandler::class
    ],

    'user_providers' => [
        /* provider_alias => user_provider_id */
    ],

    'strategy' => \StephBug\Firewall\Factory\Routing\Strategy\RouteMatchedStrategy::class
];