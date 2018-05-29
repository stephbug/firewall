<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Bootstrap;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use StephBug\Firewall\Factory\Bootstrap\AuthenticationService;
use StephBugTest\Firewall\App\HasTestBuilder;
use StephBugTest\Firewall\Mock\SomeAuthenticationServiceFactory;
use StephBugTest\Firewall\Unit\TestCase;

class AuthenticationServiceTest extends TestCase
{
    use HasTestBuilder;

    /**
     * @test
     */
    public function it_register_simple_middleware(): void
    {
        $bt = new AuthenticationService($this->getApplication());
        $builder = $this->getFirewallBuilder();

        $builder->setRequest(new Request());

        $services = new Collection(['foo']);
        $this->map->expects($this->once())->method('matches')->willReturn($services);

        $response = $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));
        $this->assertEquals('foobar', $response);

        $middleware = $builder->middleware();
        $this->assertEquals('foo', array_pop($middleware));
    }

    /**
     * @test
     */
    public function it_register_authentication_service_factory(): void
    {
        $bt = new AuthenticationService($this->getApplication());
        $builder = $this->getFirewallBuilder();

        $builder->setRequest(new Request());

        $services = new Collection([new SomeAuthenticationServiceFactory()]);
        $this->map->expects($this->once())->method('matches')->willReturn($services);

        $response = $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));
        $this->assertEquals('foobar', $response);

        $middleware = $builder->middleware();
        $this->assertEquals('foo_firewall', array_pop($middleware));

        $providers = $builder->authenticationProviders();
        $this->assertEquals('foo_provider', array_pop($providers));

        $entrypoints = $builder->entrypoints();
        $this->assertEquals('foo_entrypoint', array_pop($entrypoints));
    }
}