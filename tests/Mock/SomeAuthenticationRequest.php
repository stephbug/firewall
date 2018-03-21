<?php

declare(strict_types=1);

namespace StephBugTest\Firewall\Mock;

use Illuminate\Http\Request as IlluminateRequest;
use StephBug\SecurityModel\Application\Http\Request\AuthenticationRequest;
use Symfony\Component\HttpFoundation\Request;

class SomeAuthenticationRequest implements AuthenticationRequest
{
    /**
     * @var mixed
     */
    private $extract;

    /**
     * @var bool
     */
    private $match;

    public function __construct($extract = null, bool $match)
    {
        $this->extract = $extract;
        $this->match = $match;
    }

    public function extract(IlluminateRequest $request)
    {
        return $this->extract;
    }


    public function matches(Request $request)
    {
        return $this->match;
    }
}