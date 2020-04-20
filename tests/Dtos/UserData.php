<?php

namespace Cerbero\LaravelDto\Dtos;

use Carbon\Carbon;
use Cerbero\LaravelDto\Dto;

use const Cerbero\Dto\PARTIAL;
use const Cerbero\Dto\IGNORE_UNKNOWN_PROPERTIES;

/**
 * The data transfer object for the User model.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $rememberToken
 * @property Carbon|null $createdAt
 * @property Carbon|null $updatedAt
 * @property UserData[] $friends
 * @property PostData[] $posts
 * @property CommentData[] $comments
 * @property CommentData[] $postComments
 * @property ImageData[] $images
 */
class UserData extends Dto
{
    /**
     * The default flags.
     *
     * @var int
     */
    protected static $defaultFlags = PARTIAL | IGNORE_UNKNOWN_PROPERTIES;
}
