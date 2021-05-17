<?php

namespace Cerbero\LaravelDto;

use Cerbero\Dto\Dto as BaseDto;
use Cerbero\Dto\Manipulators\Listener as BaseListener;
use Cerbero\LaravelDto\Manipulators\Listener;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use Traversable;

use const Cerbero\Dto\CAST_PRIMITIVES;
use const Cerbero\Dto\IGNORE_UNKNOWN_PROPERTIES;
use const Cerbero\Dto\NONE;
use const Cerbero\Dto\PARTIAL;

/**
 * The data transfer object.
 *
 */
abstract class Dto extends BaseDto implements Arrayable, Jsonable
{
    use Macroable;

    /**
     * Retrieve an instance of DTO from the given or current request
     *
     * @param Request|null $request
     * @param int $flags
     * @return self
     */
    public static function fromRequest(Request $request = null, int $flags = NONE): self
    {
        $data = $request ? $request->all() : RequestFacade::all();

        return static::make($data, $flags | PARTIAL | IGNORE_UNKNOWN_PROPERTIES);
    }

    /**
     * Retrieve an instance of DTO from the request
     *
     * @param Model $model
     * @param int $flags
     * @return self
     */
    public static function fromModel(Model $model, int $flags = NONE): self
    {
        return static::make($model->toArray(), $flags | CAST_PRIMITIVES | PARTIAL | IGNORE_UNKNOWN_PROPERTIES);
    }

    /**
     * Retrieve an instance of DTO from the given source
     *
     * @param mixed $source
     * @param int $flags
     * @return self
     */
    public static function from($source, int $flags = NONE): self
    {
        if ($source instanceof Enumerable) {
            $source = $source->all();
        } elseif ($source instanceof Arrayable) {
            $source = $source->toArray();
        } elseif ($source instanceof Jsonable) {
            $source = json_decode($source->toJson(), true);
        } elseif ($source instanceof JsonSerializable) {
            $source = $source->jsonSerialize();
        } elseif ($source instanceof Traversable) {
            $source = iterator_to_array($source);
        }

        return static::make((array) $source, $flags);
    }

    /**
     * Retrieve the default flags
     *
     * @return int
     */
    public static function getDefaultFlags(): int
    {
        return Config::get('dto.flags') | static::$defaultFlags;
    }

    /**
     * Retrieve the listener instance
     *
     * @return BaseListener
     */
    protected function getListener(): BaseListener
    {
        return Listener::instance();
    }
}
