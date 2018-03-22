<?php

declare(strict_types=1);

namespace StephBug\Firewall;

use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use StephBug\Firewall\Factory\Builder;

class Processor
{
    /**
     * @var Pipeline
     */
    private $pipeline;

    /**
     * @var array
     */
    private $bootstraps;

    public function __construct(Pipeline $pipeline, array $bootstraps)
    {
        $this->pipeline = $pipeline;
        $this->bootstraps = $bootstraps;
    }

    public function process(Collection $services, Request $request): Collection
    {
        return $services->map(function (Builder $builder) use ($request) {
            return $this->pipeline
                ->via('compose')
                ->through($this->bootstraps)
                ->send($builder)
                ->then(function () use ($builder) {
                    return $builder->middleware();
                });
        });
    }
}