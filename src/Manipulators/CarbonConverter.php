<?php

namespace Cerbero\LaravelDto\Manipulators;

use Carbon\Carbon;
use Cerbero\Dto\Manipulators\ValueConverter;

/**
 * The date time converter.
 *
 */
class CarbonConverter implements ValueConverter
{
    /**
     * Convert the given value to be exported from a DTO.
     *
     * @param mixed $value
     * @return mixed
     */
    public function fromDto($value)
    {
        return $value->toAtomString();
    }

    /**
     * Convert the given value to be imported into a DTO.
     *
     * @param mixed $value
     * @return mixed
     */
    public function toDto($value)
    {
        return Carbon::parse($value);
    }
}
