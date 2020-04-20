<?php

namespace Cerbero\LaravelDto\Dtos;

use Carbon\Carbon;
use Cerbero\LaravelDto\Dto;

use const Cerbero\Dto\PARTIAL;
use const Cerbero\Dto\IGNORE_UNKNOWN_PROPERTIES;

/**
 * The data transfer object for the Post model.
 *
 * @property int $id
 * @property bool $isPrivate
 * @property string $title
 * @property string $body
 * @property int $userId
 * @property Carbon|null $createdAt
 * @property Carbon|null $updatedAt
 * @property CommentData[] $comments
 * @property ImageData[] $images
 * @property UserData $writer
 */
class PostData extends Dto
{
    /**
     * The default flags.
     *
     * @var int
     */
    protected static $defaultFlags = PARTIAL | IGNORE_UNKNOWN_PROPERTIES;
}
