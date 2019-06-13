<?php

namespace Abbarbosa\LittleBoy\Framework;

use \Closure;

interface iMiddleware
{
    public function handle($object, Closure $next);

}
