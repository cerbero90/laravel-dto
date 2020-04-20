<?php

namespace Cerbero\LaravelDto\Console;

/**
 * The default DTO qualifier.
 *
 */
class DefaultDtoQualifier implements DtoQualifierContract
{
    /**
     * Retrieve the fully qualified DTO class name for the given model.
     *
     * @param string $model
     * @return string
     */
    public function qualify(string $model): string
    {
        $segments = explode('\\', $model);
        $baseName = array_pop($segments);
        $segments[] = 'Dtos';
        $segments[] = $baseName . 'Data';

        return implode('\\', $segments);
    }
}
