<?php

namespace Cerbero\LaravelDto;

use Cerbero\LaravelDto\Providers\LaravelDtoServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

/**
 * The package test case.
 *
 */
abstract class TestCase extends OrchestraTestCase
{
    /**
     * Retrieve the package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [LaravelDtoServiceProvider::class];
    }

    /**
     * Define the environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']['database.default'] = 'testbench';

        $app['config']['database.connections.testbench'] = [
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ];
    }
}
