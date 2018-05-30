<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use StephBug\Firewall\Factory;
use StephBug\Firewall\Factory\Bootstrap\AuthenticationRegistry;
use StephBug\Firewall\Factory\Builder;
use StephBug\Firewall\Manager;
use StephBugTest\Firewall\App\HasTestBuilder;

class FactoryTest extends TestCase
{
    use HasTestBuilder;

    /**
     * @test
     */
    public function it_build_authentication_services(): void
    {
        $app = $this->getApplication();

        // Factory class do to much job and could be split with a processor
        $manager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $manager->expects($this->once())->method('hasFirewall')->willReturn(true);

        $builder = $this->getFirewallBuilder();
        $manager->expects($this->once())->method('guard')->willReturn($builder);
        $bootstraps = [$this->getBootstrap($app)];

        $factory = new Factory($manager, new Pipeline($app), $bootstraps);

        $collection = $factory->raise(['foo_bar'], new Request());

        $this->assertEquals(['foo_bar' => ['test.foo']], $collection->toArray());
    }

    private function getBootstrap(Application $app)
    {
        return new class($app) extends AuthenticationRegistry
        {
            public function compose(Builder $builder, \Closure $make)
            {
                $this->registerFirewall('test.foo', $builder);

                return $make($builder);
            }
        };
    }
}