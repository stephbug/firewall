<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Application\Exception\AuthenticationHandler;
use StephBug\Firewall\Application\Exception\AuthorizationHandler;
use StephBug\Firewall\Application\Exception\ContextualHandler;
use StephBug\Firewall\Application\Exception\SecurityHandler;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Contracts\FirewallExceptionRegistry;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorage;
use StephBug\SecurityModel\Guard\Authentication\TrustResolver;

class FirewallExceptionHandler implements FirewallExceptionRegistry
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
        $serviceId = $this->registerExceptionHandler($builder);

        $builder((new PayloadFactory())->setFirewall($serviceId));

        if ($entrypoints = $builder->entrypoints()) {
            $this->whenResolvingExceptionHandler($serviceId, $entrypoints, $builder->context()->entrypointId());
        }

        return $make($builder);
    }

    private function registerExceptionHandler(Builder $builder): string
    {
        $id = 'firewall.exception_handler.' . $builder->contextKey()->toString($builder->context());

        $this->app->bind($id, function (Application $app) use ($builder) {
            return new SecurityHandler(
                $this->contextualHandlerInstance($builder),
                $app->make(AuthenticationHandler::class),
                $app->make(AuthorizationHandler::class)
            );
        });

        return $id;
    }

    private function contextualHandlerInstance(Builder $builder): ContextualHandler
    {
        return new ContextualHandler(
            $this->app->make(TokenStorage::class),
            $builder->contextKey()->key($builder->context()),
            $this->app->make(TrustResolver::class),
            $builder->context()->isStateless(),
            $this->resolveHandler($builder->context()->entrypointId()),
            $this->resolveHandler($builder->context()->unauthorizedId())
        );
    }

    private function resolveHandler(string $handler = null)
    {
        return $handler ? $this->app->make($handler) : null;
    }

    private function whenResolvingExceptionHandler(string $serviceId, array $entrypoints, string $entrypointId = null)
    {
        foreach ($entrypoints as $entrypoint) {
            if ($entrypoint === $entrypointId) {
                continue;
            }

            $this->app->resolving($serviceId,
                function (SecurityHandler $securityHandler, Application $app) use ($entrypoint) {
                    $securityHandler->setEntrypoint(
                        $app->make($entrypoint)
                    );
                });
        }
    }
}