<?php

namespace Cerbero\LaravelDto;

use Cerbero\LaravelDto\Console\DtoQualifierContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Tests for MakeDtoCommand.
 *
 */
class MakeDtoCommandTest extends TestCase
{
    /**
     * Setup the test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');

        $this->beforeApplicationDestroyed(function () {
            (new Filesystem)->deleteDirectory($this->app->path(), true);
        });
    }

    /**
     * @test
     */
    public function generates_dtos()
    {
        $this->placeModelsInPath('domain', function (SplFileInfo $file) {
            $domain = Str::plural($file->getFilenameWithoutExtension());
            return $this->app->path("{$domain}/{$file->getFilename()}");
        });

        $this->artisan('make:dto', ['name' => 'App/Users/User'])
            ->expectsOutput('App\Comments\Dtos\CommentData created successfully.')
            ->expectsOutput('App\Images\Dtos\ImageData created successfully.')
            ->expectsOutput('App\Posts\Dtos\PostData created successfully.')
            ->expectsOutput('App\Users\Dtos\UserData created successfully.');

        $this->assertSameContent('user_data_in_domain.stub', $this->app->path('Users/Dtos/UserData.php'));
        $this->assertSameContent('post_data_in_domain.stub', $this->app->path('Posts/Dtos/PostData.php'));
        $this->assertSameContent('comment_data_in_domain.stub', $this->app->path('Comments/Dtos/CommentData.php'));
        $this->assertSameContent('image_data_in_domain.stub', $this->app->path('Images/Dtos/ImageData.php'));

        $this->artisan('make:dto', ['name' => 'App/Users/User'])
            ->expectsOutput('App\Users\Dtos\UserData already exists!');

        $this->artisan('make:dto', ['name' => 'App/Users/User', '--force' => true])
            ->expectsOutput('App\Users\Dtos\UserData created successfully.');
    }

    /**
     * Copy testing models in the path resolved by the given callback
     *
     * @param string $modelsDir
     * @param callable $resolvePath
     * @return void
     */
    protected function placeModelsInPath(string $modelsDir, callable $resolvePath): void
    {
        $models = Finder::create()->files()->in(__DIR__ . "/Database/Models/{$modelsDir}")->name('*.php');

        foreach ($models as $model) {
            $path = $resolvePath($model);

            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            copy($model->getPathname(), $path);

            require_once $path;
        }
    }

    /**
     * Assert the given stub content is the same of the file in the provided path
     *
     * @param string $stub
     * @param string $path
     * @return void
     */
    protected function assertSameContent(string $stub, string $path): void
    {
        $content = file_get_contents(__DIR__ . '/stubs/' . $stub);

        $this->assertSame($content, file_get_contents($path));
    }

    /**
     * @test
     */
    public function generates_dtos_with_custom_qualifier()
    {
        $this->swap(DtoQualifierContract::class, new TestDtoQualifier);

        $this->placeModelsInPath('app', function (SplFileInfo $file) {
            return $this->app->path($file->getFilename());
        });

        $this->artisan('make:dto', ['name' => 'App/User'])
            ->expectsOutput('App\Foo\CommentData created successfully.')
            ->expectsOutput('App\Foo\ImageData created successfully.')
            ->expectsOutput('App\Foo\PostData created successfully.')
            ->expectsOutput('App\Foo\UserData created successfully.');

        $this->assertSameContent('user_data_in_foo.stub', $this->app->path('Foo/UserData.php'));
        $this->assertSameContent('post_data_in_foo.stub', $this->app->path('Foo/PostData.php'));
        $this->assertSameContent('comment_data_in_foo.stub', $this->app->path('Foo/CommentData.php'));
        $this->assertSameContent('image_data_in_foo.stub', $this->app->path('Foo/ImageData.php'));
    }
}
