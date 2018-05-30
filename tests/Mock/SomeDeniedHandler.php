<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Mock;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthorizationException;
use StephBug\SecurityModel\Application\Http\Response\Unauthorized;
use Symfony\Component\HttpFoundation\Response;

class SomeDeniedHandler implements Unauthorized
{
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function handle(Request $request, AuthorizationException $exception): Response
    {
        return new Response($this->message);
    }
}