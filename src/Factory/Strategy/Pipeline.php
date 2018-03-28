<?php

declare(strict_types=1);

namespace StephBug\Firewall\Factory\Strategy;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline as BasePipeline;
use StephBug\Firewall\Application\Exception\DebugFirewall;
use StephBug\SecurityModel\Application\Exception\SecurityException;
use Symfony\Component\HttpFoundation\Response;

class Pipeline extends BasePipeline
{
    /**
     * @var DebugFirewall
     */
    private $debug;

    public function __construct(Application $app, DebugFirewall $debug)
    {
        parent::__construct($app);

        $this->debug = $debug;
    }

    protected function prepareDestination(\Closure $destination)
    {
        return function ($passable) use ($destination) {
            try {
                return $destination($passable);
            } catch (SecurityException $exception) {
                return $this->handleSecurityException($passable, $exception);
            }
        };
    }

    protected function carry()
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                try {
                    $slice = parent::carry();

                    $callable = $slice($stack, $pipe);

                    return $callable($passable);
                } catch (SecurityException $exception) {
                    return $this->handleSecurityException($passable, $exception);
                }
            };
        };
    }

    protected function handleSecurityException($passable, SecurityException $exception): Response
    {
        if (!$passable instanceof Request) {
            throw $exception;
        }

        // wip report on global
        logger('security exception', ['context' => $exception]);

        return $this->debug->handle($passable, $exception);
    }
}