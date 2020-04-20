<?php

namespace Cerbero\LaravelDto\Dtos;

use Carbon\Carbon;
use Cerbero\LaravelDto\Dto;

use const Cerbero\Dto\PARTIAL;
use const Cerbero\Dto\IGNORE_UNKNOWN_PROPERTIES;

/**
 * The data transfer object for the Comment model.
 *
 * @property int $id
 * @property string $body
 * @property int $userId
 * @property int $postId
 * @property Carbon|null $createdAt
 * @property Carbon|null $updatedAt
 * @property UserData $user
 * @property PostData $post
 */
class CommentData extends Dto
{
    /**
     * The default flags.
     *
     * @var int
     */
    protected static $defaultFlags = PARTIAL | IGNORE_UNKNOWN_PROPERTIES;
}
