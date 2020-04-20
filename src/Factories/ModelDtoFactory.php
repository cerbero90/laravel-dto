<?php

namespace Cerbero\LaravelDto\Factories;

use Illuminate\Support\Str;

/**
 * The model DTO factory.
 *
 */
class ModelDtoFactory extends DtoFactory
{
    /**
     * Retrieve the value for the given property from the provided source
     *
     * @param string $property
     * @param mixed $source
     * @return mixed
     */
    protected function getPropertyValueFromSource(string $property, $source)
    {
        $snakeProperty = Str::snake($property);

        if (array_key_exists($snakeProperty, $attributes = $source->getAttributes())) {
            return $attributes[$snakeProperty];
        }

        if ($source->relationLoaded($snakeProperty)) {
            return $source->$snakeProperty->toArray();
        }

        $camelProperty = Str::camel($property);

        if ($source->relationLoaded($camelProperty)) {
            return $source->$camelProperty->toArray();
        }

        return static::MISSING_PROPERTY_TOKEN;
    }
}
