<?php

namespace Cerbero\LaravelDto;

use Cerbero\LaravelDto\Console\DtoQualifierContract;

/**
 * The DTO qualifier for tests.
 *
 */
class TestDtoQualifier implements DtoQualifierContract
{
    /**
     * Retrieve the fully qualified DTO class name for the given model.
     *
     * @param string $model
     * @return string
     */
    public function qualify(string $model): string
    {
        return 'App\Foo\\' . class_basename($model) . 'Data';
    }
}
