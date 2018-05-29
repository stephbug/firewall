<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Unit\Factory\Bootstrap;

use StephBugTest\Firewall\App\HasTestBuilder;
use StephBugTest\Firewall\Unit\TestCase;

class ImpersonateUserTest extends TestCase
{
    use HasTestBuilder;

    /**
     * @test
     */
    public function it_register_impersonate_service(): void
    {
    }
}