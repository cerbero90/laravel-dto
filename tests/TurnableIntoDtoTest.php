<?php

namespace Cerbero\LaravelDto;

use Cerbero\Dto\Exceptions\DtoNotFoundException;
use Cerbero\LaravelDto\Database\Models\Tests\Comment;
use Cerbero\LaravelDto\Database\Models\Tests\Image;
use Cerbero\LaravelDto\Database\Models\Tests\Post;
use Cerbero\LaravelDto\Database\Models\Tests\User;
use Cerbero\LaravelDto\Dtos\CommentData;
use Cerbero\LaravelDto\Dtos\ImageData;
use Cerbero\LaravelDto\Dtos\PostData;
use Cerbero\LaravelDto\Dtos\RequestData;
use Cerbero\LaravelDto\Dtos\UserData;
use InvalidArgumentException;

use const Cerbero\Dto\ARRAY_DEFAULT_TO_EMPTY_ARRAY;
use const Cerbero\Dto\CAST_PRIMITIVES;
use const Cerbero\Dto\IGNORE_UNKNOWN_PROPERTIES;
use const Cerbero\Dto\PARTIAL;

/**
 * Tests for TurnsIntoDto.
 *
 */
class TurnableIntoDtoTest extends TestCase
{
    /**
     * Setup the test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__ . '/Database/factories');

        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
    }

    /**
     * @test
     */
    public function model_can_turn_into_dto()
    {
        $user = factory(User::class)->create();

        $posts = factory(Post::class, 2)->create([
            'user_id' => $user->id,
        ]);

        factory(Comment::class, 3)->create([
            'user_id' => $user->id,
            'post_id' => $posts[0]->id,
        ]);

        $posts[0]->images()->save(factory(Image::class)->make());

        $user->load('posts.comments', 'posts.images', 'postComments');

        $dto = $user->toDto(UserData::class);

        $userProps = ['id', 'name', 'email', 'password', 'rememberToken', 'createdAt', 'updatedAt', 'posts', 'postComments'];
        $postProps = ['id', 'isPrivate', 'title', 'body', 'userId', 'createdAt', 'updatedAt', 'comments', 'images'];
        $commentProps = ['id', 'body', 'userId', 'postId', 'createdAt', 'updatedAt'];
        $imageProps = ['id', 'url', 'imageableType', 'imageableId', 'createdAt', 'updatedAt'];

        $this->assertInstanceOf(UserData::class, $dto);
        $this->assertSame(PARTIAL | IGNORE_UNKNOWN_PROPERTIES | CAST_PRIMITIVES, $dto->getFlags());
        $this->assertSame(1, $dto->id);
        $this->assertSame($userProps, $dto->getPropertyNames());
        $this->assertInstanceOf(CommentData::class, $dto->postComments[0]);
        $this->assertCount(3, $dto->postComments);
        $this->assertInstanceOf(PostData::class, $dto->posts[0]);
        $this->assertSame($postProps, $dto->posts[0]->getPropertyNames());
        $this->assertInstanceOf(CommentData::class, $dto->posts[0]->comments[0]);
        $this->assertSame($commentProps, $dto->posts[0]->comments[0]->getPropertyNames());
        $this->assertInstanceOf(ImageData::class, $dto->posts[0]->images[0]);
        $this->assertSame($imageProps, $dto->posts[0]->images[0]->getPropertyNames());
    }

    /**
     * @test
     */
    public function request_can_turn_into_dto()
    {
        $data = [
            'foo' => 123,
            'bar' => true,
            'baz' => null,
        ];

        $request = new TestRequest($data);
        $dto = $request->toDto(ARRAY_DEFAULT_TO_EMPTY_ARRAY);

        $this->assertInstanceOf(RequestData::class, $dto);
        $this->assertSame(ARRAY_DEFAULT_TO_EMPTY_ARRAY, $dto->getFlags());
        $this->assertSame($data, $dto->toArray());
    }

    /**
     * @test
     */
    public function turns_into_dto_via_custom_factory()
    {
        $post = factory(Post::class)->create();
        $dto = $post->toDto(PostData::class);

        $this->assertSame(1, $post->id);
        $this->assertInstanceOf(PostData::class, $dto);
        $this->assertSame(999, $dto->id);
    }

    /**
     * @test
     */
    public function fails_if_no_dto_is_provided()
    {
        $exception = new InvalidArgumentException('DTO to turn [' . Comment::class . '] into not specified');
        $this->expectExceptionObject($exception);

        factory(Comment::class)->create()->toDto();
    }

    /**
     * @test
     */
    public function fails_if_missing_dto_is_provided()
    {
        $exception = new DtoNotFoundException('MissingDto');
        $this->expectExceptionObject($exception);

        factory(Comment::class)->create()->toDto('MissingDto');
    }
}
