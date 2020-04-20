<?php

namespace Cerbero\LaravelDto;

use Cerbero\LaravelDto\Factories\DtoFactory;

/**
 * The DTO factory for testing.
 *
 */
class TestDtoFactory extends DtoFactory
{
    /**
     * Retrieve the DTO ID via custom logic
     *
     * @return int
     */
    protected function getId(): int
    {
        return 999;
    }
}
