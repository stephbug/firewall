<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Bootstrap;

use StephBug\Firewall\Factory\Bootstrap\SerializationContext;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\User\UserProvider;
use StephBugTest\Firewall\App\HasTestBuilder;
use StephBugTest\Firewall\Unit\TestCase;

class SerializationContextTest extends TestCase
{
    use HasTestBuilder;

    /**
     * @test
     */
    public function it_register_serialization_context(): void
    {
        $app=  $this->getApplication();
        $bt = new SerializationContext($app);
        $builder = $this->getFirewallBuilder();
        $this->assertEmpty($builder->middleware());

        $this->context->expects($this->once())->method('isStateless')->willReturn(false);
        $key = $this->getMockBuilder(SecurityKey::class)->disableOriginalConstructor()->getMock();
        $key->expects($this->once())->method('value')->willReturn('bar');
        $this->keyContext->expects($this->once())->method('key')->willReturn($key);

        $app->bind('foo', $this->getMockForAbstractClass(UserProvider::class));
        $this->userProviders->expects($this->once())->method('toArray')->willReturn(['foo','baz']);
        $this->userProviders->expects($this->once())->method('get')->willReturn('foo');

        $response = $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));
        $this->assertEquals('foobar', $response);

        $middleware = $builder->middleware();
        $this->assertEquals('firewall.context_bar', array_pop($middleware));
        $this->assertTrue($app->bound('firewall.context_bar'));
    }

    /**
     * @test
     */
    public function it_does_not_register_serialization_context_when_context_is_stateless(): void
    {
        $bt = new SerializationContext($this->getApplication());
        $builder = $this->getFirewallBuilder();

        $this->context->expects($this->once())->method('isStateless')->willReturn(true);

        $response = $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));
        $this->assertEquals('foobar', $response);

        $this->assertEmpty($builder->middleware());
    }
}