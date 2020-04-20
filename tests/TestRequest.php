<?php

namespace Cerbero\LaravelDto;

use Cerbero\LaravelDto\Dtos\RequestData;
use Cerbero\LaravelDto\Traits\TurnsIntoDto;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

/**
 * Sample request for testing.
 *
 */
class TestRequest extends Request
{
    use TurnsIntoDto;

    /**
     * The DTO to turn into.
     *
     * @var string
     */
    protected $dtoClass = RequestData::class;

    /**
     * Override method to simulate route binding
     *
     * @param string|null $param
     * @param mixed $default
     * @return \Illuminate\Routing\Route
     */
    public function route($param = null, $default = null)
    {
        return (new Route('GET', '/', 'index'))->bind($this);
    }
}
