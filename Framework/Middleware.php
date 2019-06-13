<?php

namespace Abbarbosa\LittleBoy\Framework;

use Abbarbosa\LittleBoy\Framework\iMiddleware;
use \Closure;
use \InvalidArgumentException;

/**
 * TO DO: Implementar no router
 */
class Middleware
{
    private $middlewares;
    public function __construct(array $middlewares = [])
    {
        $this->middlewares = $middlewares;
    }

    public function layer($middlewares)
    {
        if ($middlewares instanceof iMiddleware) {
            $middlewares = $middlewares->toArray();
        }
        if ($middlewares instanceof iMiddleware) {
            $middlewares = [$middlewares];
        }
        if (!is_array($middlewares)) {
            throw new InvalidArgumentException(get_class($middlewares) . " is not a valid iMiddleware layer.");
        }
        return new static(array_merge($this->middlewares, $middlewares));
    }

    public function handle($object, Closure $next)
    {
        $nextFunction = $this->createnextFunction($next);
        $middlewares = array_reverse($this->middlewares);
        $completeiMiddleware = array_reduce($middlewares, function ($nextLayer, $layer) {
            return $this->createLayer($nextLayer, $layer);
        }, $nextFunction);
        return $completeiMiddleware($object);
    }

    public function toArray()
    {
        return $this->middlewares;
    }

    private function createNextFunction(Closure $next)
    {
        return function ($object) use ($next) {
            return $next($object);
        };
    }

    private function createLayer($nextLayer, $layer)
    {
        return function ($object) use ($nextLayer, $layer) {
            return $layer->handle($object, $nextLayer);
        };
    }
}
