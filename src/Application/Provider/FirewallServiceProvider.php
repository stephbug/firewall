<?php

declare(strict_types=1);

namespace StephBug\Firewall\Application\Provider;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\ServiceProvider;
use StephBug\Firewall\Application\Http\Middleware\Firewall;
use StephBug\Firewall\Application\Http\Middleware\SessionContext;
use StephBug\Firewall\Factory\LogoutManager;
use StephBug\Firewall\Factory\RecallerManager;
use StephBug\Firewall\Manager;
use StephBug\Firewall\Processor;

class FirewallServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    public function register(): void
    {
        $this->mergeConfig();

        $this->app->singleton(Manager::class);

        $this->app->singleton(RecallerManager::class);

        $this->app->singleton(LogoutManager::class);

        $this->app->bind(Processor::class, function (Application $app) {
            return new Processor(
                new Pipeline($app),
                $app->make('config')->get('firewall.bootstraps', [])
            );
        });
    }

    public function boot(): void
    {
        $this->publishes(
            [$this->getConfigPath() => config_path('firewall.php')],
            'config'
        );

        // $this->app->bind(Factory::class);

        $this->app->singleton(SessionContext::class);

        $this->app->singleton(Firewall::class, function (Application $app) {
            return new Firewall(
                $app->make($app->make('config')->get('firewall.strategy'))
            );
        });
    }

    public function provides(): array
    {
        return [Manager::class, RecallerManager::class, LogoutManager::class, Processor::class];
    }

    protected function mergeConfig(): void
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'firewall');
    }

    protected function getConfigPath(): string
    {
        return __DIR__ . '/../../../config/firewall.php';
    }
}