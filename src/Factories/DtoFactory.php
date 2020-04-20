<?php

namespace Cerbero\LaravelDto\Factories;

use Cerbero\Dto\DtoPropertiesMapper;
use Cerbero\LaravelDto\Dto;
use Illuminate\Support\Str;

use const Cerbero\Dto\NONE;

/**
 * The DTO factory.
 *
 */
class DtoFactory implements DtoFactoryContract
{
    /**
     * The token indicating that a property is missing. Workaround to distinguish between NULL and missing values
     *
     * @var string
     */
    protected const MISSING_PROPERTY_TOKEN = 'cerbero_laravel_dto_missing_property_token';

    /**
     * Retrieve an instance of the given DTO with data from the provided source.
     *
     * @param string $dto
     * @param mixed $source
     * @param int $flags
     * @return Dto
     */
    public function make(string $dto, $source, int $flags = NONE): Dto
    {
        $data = [];
        $properties = DtoPropertiesMapper::for($dto)->getNames();

        foreach ($properties as $property) {
            $value = $this->getPropertyFromSource($property, $source);

            if ($value === static::MISSING_PROPERTY_TOKEN) {
                continue;
            }

            $data[$property] = $value;
        }

        return $dto::make($data, $flags);
    }

    /**
     * Retrieve the given property from the provided source
     *
     * @param string $property
     * @param mixed $source
     * @return mixed
     */
    protected function getPropertyFromSource(string $property, $source)
    {
        $accessor = 'get' . Str::studly($property);

        if (method_exists($this, $accessor)) {
            return call_user_func([$this, $accessor], $source, $property);
        }

        return $this->getPropertyValueFromSource($property, $source);
    }

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
        $camelProperty = Str::camel($property);

        return data_get($source, $snakeProperty, data_get($source, $camelProperty, static::MISSING_PROPERTY_TOKEN));
    }
}
