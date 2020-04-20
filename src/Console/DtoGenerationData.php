<?php

namespace Cerbero\LaravelDto\Console;

use Cerbero\LaravelDto\Dto;
use Illuminate\Console\OutputStyle;
use Illuminate\Database\Eloquent\Model;

/**
 * The DTO generation data.
 *
 * @property string $modelClass
 * @property Model $model
 * @property bool $forced
 * @property OutputStyle $output
 */
class DtoGenerationData extends Dto
{
    //
}
