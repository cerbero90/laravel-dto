<?php

namespace Cerbero\LaravelDto;

use Cerbero\LaravelDto\Console\DtoQualifierContract;

/**
 * Testing listener for the user DTO.
 *
 */
class TestUserDataListener
{
    /**
     * The DTO qualifier.
     *
     * @var DtoQualifierContract
     */
    protected $qualifier;

    /**
     * Instantiate the class.
     *
     * @param DtoQualifierContract $qualifier
     */
    public function __construct(DtoQualifierContract $qualifier)
    {
        $this->qualifier = $qualifier;
    }

    /**
     * Retrieve the user name
     *
     * @param string $name
     * @return string
     */
    public function getName(string $name): string
    {
        return get_class($this->qualifier);
    }
}
