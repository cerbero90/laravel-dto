<?php

use Carbon\Carbon;
use Cerbero\LaravelDto\Console\DefaultDtoQualifier;
use Cerbero\LaravelDto\Manipulators\CarbonConverter;

use const Cerbero\Dto\NONE;

return [
    /*
    |--------------------------------------------------------------------------
    | DTO class qualifier
    |--------------------------------------------------------------------------
    |
    | The name of the class that fully qualifies DTO class names when DTOs are
    | generated via Artisan. A default qualifier has been included but feel
    | free to replace it if you need DTOs to be in a different directory
    |
    */
    'qualifier' => DefaultDtoQualifier::class,

    /*
    |--------------------------------------------------------------------------
    | DTO value conversions
    |--------------------------------------------------------------------------
    |
    | Sometimes we might want a specific value type to be converted when a DTO
    | turns into an array. Below you can set what class should be converted
    | and the converter that accommodates its conversion from/to the DTO
    |
    */
    'conversions' => [
        Carbon::class => CarbonConverter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | DTO value listeners
    |--------------------------------------------------------------------------
    |
    | Whenever a DTO sets or gets one of its property values, a listener might
    | intercept such event and alter the outcome. This is handy for example
    | when a value needs to be processed before getting set or retrieved
    |
    */
    'listeners' => [
        // UserData::class => UserDataListener::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | DTO global flags
    |--------------------------------------------------------------------------
    |
    | The flags to apply to all DTOs by default. Multiple flags might be added
    | below by joining them with bitwise OR operators "|". These flags will
    | finally be merged with the default flags specified within each DTO
    |
    */
    'flags' => NONE,
];
