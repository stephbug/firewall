<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Mock;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Application\Http\Entrypoint\Entrypoint;
use Symfony\Component\HttpFoundation\Response;

class SomeEntrypoint implements Entrypoint
{
    /**
     * @var string
     */
    private $message;

    private $exception;

    public function __construct(string $message = null)
    {
        $this->message = $message;
    }

    public function startAuthentication(Request $request, AuthenticationException $exception = null): Response
    {
        $this->exception = $exception;

        $message = $this->message ?? ($exception ? $exception->getMessage() : 'no exception message defined');

        return new Response($message);
    }

    public function getException(): ? AuthenticationException
    {
        return $this->exception;
    }
}