<?php

namespace Cerbero\LaravelDto\Console;

/**
 * The DTO class qualifier interface.
 *
 */
interface DtoQualifierContract
{
    /**
     * Retrieve the fully qualified DTO class name for the given model.
     *
     * @param string $model
     * @return string
     */
    public function qualify(string $model): string;
}
