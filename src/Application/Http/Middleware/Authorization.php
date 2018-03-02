<?php

declare(strict_types=1);

namespace StephBug\Firewall\Application\Http\Middleware;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Guard\Authorizer;

class Authorization
{
    /**
     * @var Authorizer
     */
    private $authorizer;

    public function __construct(Authorizer $authorizer)
    {
        $this->authorizer = $authorizer;
    }

    public function handle(Request $request, \Closure $next, ...$attributes)
    {
        if ($attributes) {
            $this->authorizer->requireGranted($attributes, $request);
        }

        return $next($request);
    }
}