<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use StephBug\Firewall\Factory\Bootstrap\AuthenticationProvider;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\SecurityModel\Guard\Authentication\AuthenticationProviders;
use StephBug\SecurityModel\Guard\Authentication\Providers\AuthenticationProvider as SecurityAuthenticationProvider;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBugTest\Firewall\App\HasTestBuilder;
use StephBugTest\Firewall\Unit\TestCase;

class AuthenticationProviderTest extends TestCase
{
    use HasTestBuilder;

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_when_no_authentication_provider_has_been_registered(): void
    {
        $this->expectExceptionMessage('No authentication providers has been registered');

        $providers = new AuthenticationProviders();
        $builder = $this->getFirewallBuilder();
        $bt = new AuthenticationProvider($providers, $this->getApplication());

        $this->assertEmpty($builder->authenticationProviders());

        $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));
    }

    /**
     * @test
     */
    public function it_make_security_authentication_providers_aware(): void
    {
        $providers = new AuthenticationProviders();
        $app = $this->getApplication();
        $builder = $this->getFirewallBuilder();
        $bt = new AuthenticationProvider($providers, $app);

        $this->assertEmpty($builder->authenticationProviders());

        $provider = $this->getMockForAbstractClass(SecurityAuthenticationProvider::class);
        $provider->expects($this->once())->method('supports')->willReturn(true);

        $this->registerProvider($app, $builder, $provider);

        $response = $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));

        $this->assertEquals('foobar', $response);

        $token = $this->getMockForAbstractClass(Tokenable::class);

        $this->assertEquals($provider, $providers->firstSupportedProvider($token));
    }

    private function registerProvider(Application $app, Builder $builder, $provider): void
    {
        $app->instance('foobar', $provider);

        ($builder)((new PayloadFactory())->setProvider('foobar'));
    }
}