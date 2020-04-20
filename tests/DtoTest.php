<?php

namespace Cerbero\LaravelDto;

use ArrayIterator;
use Carbon\Carbon;
use Cerbero\LaravelDto\Console\DefaultDtoQualifier;
use Cerbero\LaravelDto\Database\Models\Tests\User;
use Cerbero\LaravelDto\Dtos\RequestData;
use Cerbero\LaravelDto\Dtos\UserData;
use Cerbero\LaravelDto\Manipulators\Listener;
use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

use const Cerbero\Dto\ARRAY_DEFAULT_TO_EMPTY_ARRAY;
use const Cerbero\Dto\BOOL_DEFAULT_TO_FALSE;
use const Cerbero\Dto\CAST_PRIMITIVES;
use const Cerbero\Dto\IGNORE_UNKNOWN_PROPERTIES;
use const Cerbero\Dto\PARTIAL;

/**
 * Tests for DTO.
 *
 */
class DtoTest extends TestCase
{
    /**
     * @test
     */
    public function converts_carbon_instances_when_set_or_retrieved()
    {
        $data = ['created_at' => '2000-01-01T00:00:00+00:00'];
        $dto = UserData::make($data);

        $this->assertInstanceOf(Carbon::class, $dto->createdAt);
        $this->assertSame($data, $dto->toArray());
    }

    /**
     * @test
     */
    public function creates_instance_from_model()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
        $this->withFactories(__DIR__ . '/Database/factories');

        $user = factory(User::class)->create();
        $dto = UserData::fromModel($user, BOOL_DEFAULT_TO_FALSE);
        $expectedFlags = PARTIAL | IGNORE_UNKNOWN_PROPERTIES | CAST_PRIMITIVES | BOOL_DEFAULT_TO_FALSE;

        $this->assertInstanceOf(UserData::class, $dto);
        $this->assertSame($expectedFlags, $dto->getFlags());
    }

    /**
     * @test
     */
    public function creates_instance_from_request()
    {
        $request = new TestRequest();
        $dto = RequestData::fromRequest($request, ARRAY_DEFAULT_TO_EMPTY_ARRAY);
        $expectedFlags = PARTIAL | IGNORE_UNKNOWN_PROPERTIES | ARRAY_DEFAULT_TO_EMPTY_ARRAY;

        $this->assertInstanceOf(RequestData::class, $dto);
        $this->assertSame($expectedFlags, $dto->getFlags());
    }

    /**
     * @test
     */
    public function creates_instance_from_several_sources()
    {
        $source = collect(['foo' => 123]);
        $dto = RequestData::from($source, PARTIAL);
        $this->assertInstanceOf(RequestData::class, $dto);
        $this->assertSame(PARTIAL, $dto->getFlags());

        $source = new TestRequest(['foo' => 123]);
        $dto = RequestData::from($source, PARTIAL);
        $this->assertInstanceOf(RequestData::class, $dto);
        $this->assertSame(PARTIAL, $dto->getFlags());

        $source = new TestJsonable(['foo' => 123]);
        $dto = RequestData::from($source, PARTIAL);
        $this->assertInstanceOf(RequestData::class, $dto);
        $this->assertSame(PARTIAL, $dto->getFlags());

        $source = new TestJsonSerializable(['foo' => 123]);
        $dto = RequestData::from($source, PARTIAL);
        $this->assertInstanceOf(RequestData::class, $dto);
        $this->assertSame(PARTIAL, $dto->getFlags());

        $source = new ArrayIterator(['foo' => 123]);
        $dto = RequestData::from($source, PARTIAL);
        $this->assertInstanceOf(RequestData::class, $dto);
        $this->assertSame(PARTIAL, $dto->getFlags());

        $source = ['foo' => 123];
        $dto = RequestData::from($source, PARTIAL);
        $this->assertInstanceOf(RequestData::class, $dto);
        $this->assertSame(PARTIAL, $dto->getFlags());
    }

    /**
     * @test
     */
    public function has_listener_that_can_resolve_dependencies()
    {
        Listener::instance()->listen([
            UserData::class => TestUserDataListener::class,
        ]);

        $dto = UserData::make(['name' => 'foo']);

        $this->assertSame(DefaultDtoQualifier::class, $dto->name);
    }

    /**
     * @test
     */
    public function can_be_resolved_by_container()
    {
        $this->swap('request', new TestRequest(['name' => 'foo']));

        $dto = Container::getInstance()->make(UserData::class);

        $this->assertSame('foo', $dto->name);
    }
}


class TestJsonable implements Jsonable
{
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function toJson($options = 0)
    {
        return json_encode($this->data, $options);
    }
}


class TestJsonSerializable implements JsonSerializable
{
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function jsonSerialize()
    {
        return $this->data;
    }
}
