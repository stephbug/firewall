<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Application\Exception;

use Illuminate\Http\Request;
use StephBug\Firewall\Application\Exception\ContextualHandler;
use StephBug\SecurityModel\Application\Http\Entrypoint\Entrypoint;
use StephBug\SecurityModel\Application\Http\Response\Unauthorized;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorage;
use StephBug\SecurityModel\Guard\Authentication\TrustResolver;
use StephBug\SecurityModel\User\Exception\InvalidUserStatus;
use StephBugTest\Firewall\Mock\SecurityTestKey;
use StephBugTest\Firewall\Mock\SomeAuthenticationException;
use StephBugTest\Firewall\Mock\SomeDeniedHandler;
use StephBugTest\Firewall\Mock\SomeEntrypoint;
use StephBugTest\Firewall\Unit\TestCase;

class ContextualHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_response_from_entrypoint(): void
    {
        $ins = $this->getContextualHandlerInstance(true, false);
        $exc = new SomeAuthenticationException();

        $response = $ins->startAuthentication(new Request(), $exc);
        $this->assertEquals('foo_baz', $response->getContent());
    }

    /**
     * @test
     */
    public function it_access_entrypoint_id(): void
    {
        $ins = $this->getContextualHandlerInstance(true, false);
        $this->assertInstanceOf(Entrypoint::class, $ins->entrypoint());
        $this->assertInstanceOf(SomeEntrypoint::class, $ins->entrypoint());
    }

    /**
     * @test
     */
    public function it_can_set_entrypoint(): void
    {
        $ins = $this->getContextualHandlerInstance(false, false);
        $this->assertNull($ins->entrypoint());

        $ins->setEntrypoint($ep = new SomeEntrypoint('baz'));
        $this->assertEquals($ep, $ins->entrypoint());
    }

    /**
     * @test
     */
    public function it_can_set_unauthorized_handler(): void
    {
        $ins = $this->getContextualHandlerInstance(false, false);
        $this->assertNull($ins->deniedHandler());

        $ins->setUnauthorized($ep = new SomeDeniedHandler('baz'));
        $this->assertEquals($ep, $ins->deniedHandler());
    }

    /**
     * @test
     */
    public function it_access_denied_handler_id(): void
    {
        $ins = $this->getContextualHandlerInstance(false, true);

        $this->assertInstanceOf(Unauthorized::class, $ins->deniedHandler());
        $this->assertInstanceOf(SomeDeniedHandler::class, $ins->deniedHandler());
    }

    /**
     * @test
     */
    public function it_erase_token_storage_when_exception_instance_of_security_invalid_status(): void
    {
        $ins = $this->getContextualHandlerInstance(true, false);

        $this->storage->expects($this->once())->method('setToken');

        $exc = new InvalidUserStatus('foo');

        $ins->startAuthentication(new Request(), $exc);
    }

    /**
     * @test
     * @expectedException \StephBugTest\Firewall\Mock\SomeAuthenticationException
     */
    public function it_raise_caught_exception_when_no_entrypoint_has_been_defined(): void
    {
        $this->expectExceptionMessage('foo');

        $ins = $this->getContextualHandlerInstance(false, true);

        $ins->startAuthentication(new Request(), new SomeAuthenticationException('foo'));
    }

    private function getContextualHandlerInstance(bool $entrypoint, bool $unauthorized): ContextualHandler
    {
        return new ContextualHandler(
            $this->storage,
            new SecurityTestKey('foo'),
            $this->getMockForAbstractClass(TrustResolver::class),
            false,
            $entrypoint ? new SomeEntrypoint('foo_baz') : null,
            $unauthorized ? new SomeDeniedHandler('baz_baz') : null
        );
    }

    private $storage;

    protected function setUp(): void
    {
        $this->storage = $this->getMockForAbstractClass(TokenStorage::class);
    }
}