<?php

namespace Cerbero\LaravelDto\Traits;

use Cerbero\Dto\Exceptions\DtoNotFoundException;
use Cerbero\LaravelDto\Dto;
use Cerbero\LaravelDto\Factories\DtoFactory;
use Cerbero\LaravelDto\Factories\DtoFactoryContract;
use Cerbero\LaravelDto\Factories\ModelDtoFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

use const Cerbero\Dto\CAST_PRIMITIVES;
use const Cerbero\Dto\NONE;

/**
 * The trait to turn an object into a DTO.
 *
 */
trait TurnsIntoDto
{
    /**
     * Retrieve a DTO instance based on the current object data.
     *
     * @param string|int|null $dto
     * @param int $flags
     * @return Dto
     * @throws InvalidArgumentException
     * @throws DtoNotFoundException
     */
    public function toDto($dto = null, int $flags = NONE): Dto
    {
        $flags = is_int($dto) ? $dto : $flags;
        $flags |= $this instanceof Model ? CAST_PRIMITIVES : NONE;
        $dto = $this->getDtoToTurnInto($dto);

        return $this->getDtoFactory()->make($dto, $this, $flags);
    }

    /**
     * Retrieve the DTO class to turn the current object into
     *
     * @param mixed $dto
     * @return string
     * @throws InvalidArgumentException
     * @throws DtoNotFoundException
     */
    protected function getDtoToTurnInto($dto): string
    {
        $dto = is_string($dto) ? $dto : $this->getDtoClass();

        if (!$dto) {
            throw new InvalidArgumentException('DTO to turn [' . static::class . '] into not specified');
        } elseif (!is_subclass_of($dto, Dto::class)) {
            throw new DtoNotFoundException($dto);
        }

        return $dto;
    }

    /**
     * Retrieve the DTO class
     *
     * @return string|null
     */
    protected function getDtoClass(): ?string
    {
        return $this->dtoClass;
    }

    /**
     * Retrieve the DTO assembler
     *
     * @return DtoFactoryContract
     */
    protected function getDtoFactory(): DtoFactoryContract
    {
        return $this instanceof Model ? new ModelDtoFactory() : new DtoFactory();
    }
}
