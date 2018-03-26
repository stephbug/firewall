<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Contracts\FirewallRegistry;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\SecurityModel\Application\Http\Firewall\AnonymousAuthenticationFirewall;
use StephBug\SecurityModel\Application\Values\Security\AnonymousKey;
use StephBug\SecurityModel\Guard\Authentication\Providers\AnonymousAuthenticationProvider;
use StephBug\SecurityModel\Guard\Guard;

class AnonymousRequest implements FirewallRegistry
{
    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function compose(Builder $builder, \Closure $make)
    {
        if ($builder->context()->isAnonymous()) {
            $anonymousKey = $builder->context()->anonymousKey();

            $builder((new PayloadFactory())
                ->setFirewall($this->registerFirewall($anonymousKey))
                ->setProvider($this->registerProvider($anonymousKey))
            );
        }

        return $make($builder);
    }

    protected function registerFirewall(AnonymousKey $anonymousKey): string
    {
        $id = 'firewall.anonymous.default_authentication_firewall.' . $anonymousKey->value();

        $this->app->bind($id, function (Application $app) use ($anonymousKey) {
            return new AnonymousAuthenticationFirewall($app->make(Guard::class), $anonymousKey);
        });

        return $id;
    }

    protected function registerProvider(AnonymousKey $anonymousKey): string
    {
        $id = 'firewall.anonymous.default_authentication_provider.' . $anonymousKey->value();

        $this->app->bind($id, function () use ($anonymousKey) {
            return new AnonymousAuthenticationProvider($anonymousKey);
        });

        return $id;
    }
}