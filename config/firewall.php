<?php

return [

    'services' => [
        //'front' => [ 'factory']
    ],

    'bootstraps' => [

        \StephBug\Firewall\Factory\Bootstrap\AuthenticationProvider::class,
        \StephBug\Firewall\Factory\Bootstrap\EntrypointRegistry::class,
        \StephBug\Firewall\Factory\Bootstrap\SerializationContext::class,
        \StephBug\Firewall\Factory\Bootstrap\AuthenticationService::class,
        \StephBug\Firewall\Factory\Bootstrap\LogoutService::class,
        \StephBug\Firewall\Factory\Bootstrap\AnonymousRequest::class,
        \StephBug\Firewall\Factory\Bootstrap\AccessControl::class,
        \StephBug\Firewall\Factory\Bootstrap\FirewallExceptionHandler::class
    ],

    'context' => [

        'default' => \StephBug\Firewall\Factory\Context\DefaultFirewallContext::class
    ],

    'user_providers' => [
        //'alias' => 'serviceId'
    ]
];