<?php

namespace Cerbero\LaravelDto\Manipulators;

use Cerbero\Dto\Manipulators\Listener as BaseListener;
use Illuminate\Container\Container;

/**
 * The DTO listener.
 *
 */
class Listener extends BaseListener
{
    /**
     * Retrieve the instance of the given listener
     *
     * @param string $listener
     * @return mixed
     */
    protected function resolveListener(string $listener)
    {
        return Container::getInstance()->make($listener);
    }
}
