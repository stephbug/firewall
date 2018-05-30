<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Mock;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use StephBug\Firewall\Application\Exception\DebugFirewall;
use StephBug\SecurityModel\Application\Exception\SecurityException;
use StephBug\SecurityModel\Application\Http\Entrypoint\Entrypoint;

class SomeDebugFirewall implements DebugFirewall
{
    private $entrypoint;

    public function handle(Request $request, SecurityException $exception): Response
    {
        return new Response($exception->getMessage() ?? 'no exception message provided');
    }

    public function setEntrypoint(Entrypoint $entrypoint): void
    {
        $this->entrypoint = $entrypoint;
    }

    public function hasEntrypoint(): bool
    {
        return $this->entrypoint instanceof Entrypoint;
    }
}