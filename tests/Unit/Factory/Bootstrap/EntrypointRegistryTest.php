<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Bootstrap;

use StephBug\Firewall\Factory\Bootstrap\EntrypointRegistry;
use StephBugTest\Firewall\App\HasTestBuilder;
use StephBugTest\Firewall\Unit\TestCase;

class EntrypointRegistryTest extends TestCase
{
    use HasTestBuilder;

    /**
     * @test
     */
    public function it_make_default_entrypoint_globally_available(): void
    {
        $app = $this->getApplication();
        $bt = new EntrypointRegistry($app);
        $builder = $this->getFirewallBuilder();

        $this->context->expects($this->once())->method('entrypointId')->willReturn('bar_bar');
        $app->bind('bar_bar', 'foo_foo');

        $response = $bt->compose($builder, $this->getResponseFromLastPipe('bar_foo'));

        $this->assertEquals('bar_foo', $response);
        $this->assertEquals('bar_bar', $builder->defaultEntrypointId());
    }

    /**
     * @test
     */
    public function it_internally_name_entrypoint_if_it_not_bound_to_container(): void
    {
        $app = $this->getApplication();
        $bt = new EntrypointRegistry($app);
        $builder = $this->getFirewallBuilder();

        $this->context->expects($this->once())->method('entrypointId')->willReturn('bar_bar');
        $this->keyContext->expects($this->once())->method('toString')->willReturn('bar_foo');
        $this->assertFalse($app->bound('bar_bar'));

        $response = $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));

        $this->assertEquals('foobar', $response);
        $this->assertEquals('firewall.default_entrypoint.bar_foo', $builder->defaultEntrypointId());
        $this->assertTrue($app->bound('firewall.default_entrypoint.bar_foo'));
    }

    /**
     * @test
     */
    public function it_does_not_handle_entrypoint_if_context_has_one(): void
    {
        $app = $this->getApplication();
        $bt = new EntrypointRegistry($app);
        $builder = $this->getFirewallBuilder();

        $this->context->expects($this->once())->method('entrypointId')->willReturn(null);
        $this->assertNull($builder->defaultEntrypointId());

        $response = $bt->compose($builder, $this->getResponseFromLastPipe('foobar'));

        $this->assertEquals('foobar', $response);
        $this->assertNull($builder->defaultEntrypointId());

    }
}