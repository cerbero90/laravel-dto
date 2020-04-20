<?php

namespace Cerbero\LaravelDto\Factories;

use Cerbero\LaravelDto\Dto;

use const Cerbero\Dto\NONE;

/**
 * The DTO factory contract.
 *
 */
interface DtoFactoryContract
{
    /**
     * Retrieve an instance of the given DTO with data from the provided source.
     *
     * @param string $dto
     * @param mixed $source
     * @param int $flags
     * @return Dto
     */
    public function make(string $dto, $source, int $flags = NONE): Dto;
}
