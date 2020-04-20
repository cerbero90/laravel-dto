<?php

namespace Cerbero\LaravelDto\Dtos;

use Carbon\Carbon;
use Cerbero\LaravelDto\Dto;

use const Cerbero\Dto\PARTIAL;
use const Cerbero\Dto\IGNORE_UNKNOWN_PROPERTIES;

/**
 * The data transfer object for the Image model.
 *
 * @property int $id
 * @property string $url
 * @property string $imageableType
 * @property int $imageableId
 * @property Carbon|null $createdAt
 * @property Carbon|null $updatedAt
 */
class ImageData extends Dto
{
    /**
     * The default flags.
     *
     * @var int
     */
    protected static $defaultFlags = PARTIAL | IGNORE_UNKNOWN_PROPERTIES;
}
