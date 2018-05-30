<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use StephBug\Firewall\Factory\Bootstrap\FirewallExceptionHandler;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Factory\Payload\PayloadFactory;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorage;
use StephBug\SecurityModel\Guard\Authentication\TrustResolver;
use StephBugTest\Firewall\App\HasTestBuilder;
use StephBugTest\Firewall\Mock\SecurityTestKey;
use StephBugTest\Firewall\Mock\SomeAuthenticationException;
use StephBugTest\Firewall\Mock\SomeEntrypoint;
use StephBugTest\Firewall\Unit\TestCase;

class FirewallExceptionHandlerTest extends TestCase
{
    use HasTestBuilder;

    /**
     * @test
     */
    public function it_register_security_exception_handler(): void
    {
        $app = $this->getApplication();
        $bt = new FirewallExceptionHandler($app);
        $builder = $this->getFirewallBuilder();
        $this->assertEmpty($builder->middleware());

        $key = new SecurityTestKey('baz');

        $this->keyContext->expects($this->any())->method('key')->willReturn($key);
        $this->keyContext->expects($this->any())->method('toString')->willReturn($key);

        $response = $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));
        $this->assertEquals('foobar', $response);

        $middleware = $builder->middleware();
        $this->assertEquals('firewall.exception_handler.baz', array_pop($middleware));
    }

    /**
     * @test
     */
    public function it_set_entrypoint_returned_by_authentication_factory_on_resolving_exception_handler(): void
    {
        $app = $this->getApplication();
        $bt = new FirewallExceptionHandler($app);
        $builder = $this->getFirewallBuilder();
        $this->assertEmpty($builder->middleware());

        $key = new SecurityTestKey('baz');

        $this->keyContext->expects($this->any())->method('key')->willReturn($key);
        $this->keyContext->expects($this->any())->method('toString')->willReturn($key);

        $this->assertNull($builder->defaultEntrypointId());

        // mock entrypoint
        $this->registerFakeEntrypoint($app, $builder);

        // setup contextual handler dependencies
        $this->registerContextualDependencies($app);

        // compose
        $response = $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));
        $this->assertEquals('foobar', $response);

        $middleware = $builder->middleware();
        $this->assertEquals('firewall.exception_handler.baz', array_pop($middleware));

        // resolve exception handler
        $resolved = $app->make('firewall.exception_handler.baz');
        $exc = new SomeAuthenticationException();

        $response = $resolved->handle(new Request(), $exc);
        $this->assertEquals('foo_baz', $response->getContent());
    }

    private function registerContextualDependencies(Application $app): void
    {
        $app->bind(TokenStorage::class, function () {
            return $this->getMockForAbstractClass(TokenStorage::class);
        });

        $app->bind(TrustResolver::class, function () {
            return $this->getMockForAbstractClass(TrustResolver::class);
        });

        $this->context->expects($this->once())->method('isStateless')->willReturn(true);
    }

    private function registerFakeEntrypoint(Application $app, Builder $builder)
    {
        $app->bind('test.entrypoint', function () {
            return new SomeEntrypoint('foo_baz');
        });
        ($builder)((new PayloadFactory())->setEntrypoint('test.entrypoint'));
        $entrypoints = $builder->entrypoints();

        $this->assertEquals('test.entrypoint', array_pop($entrypoints));
    }
}