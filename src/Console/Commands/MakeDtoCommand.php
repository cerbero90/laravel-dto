<?php

namespace Cerbero\LaravelDto\Console\Commands;

use Cerbero\LaravelDto\Console\DtoGenerationData;
use Cerbero\LaravelDto\Console\Manifest;
use Cerbero\LaravelDto\Console\ModelPropertiesMapper;
use Cerbero\LaravelDto\Console\DtoQualifierContract;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Exception\InvalidArgumentException;

/**
 * The Artisan command to generate DTOs.
 *
 */
class MakeDtoCommand extends GeneratorCommand
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new DTO class';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:dto
        {name : The model to create the DTO for, e.g. App/User}
        {--f|force : Create the class even if the DTO already exists}';

    /**
     * The models use statements.
     *
     * @var array
     */
    protected $useStatements = [];

    /**
     * The DTO class qualifier.
     *
     * @var DtoQualifierContract
     */
    protected $qualifier;

    /**
     * The DTO generation manifest.
     *
     * @var Manifest
     */
    protected $manifest;

    /**
     * Instantiate the class.
     *
     * @param Filesystem $files
     * @param DtoQualifierContract $qualifier
     * @param Manifest $manifest
     */
    public function __construct(Filesystem $files, DtoQualifierContract $qualifier, Manifest $manifest)
    {
        parent::__construct($files);

        $this->qualifier = $qualifier;
        $this->manifest = $manifest;
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        parent::handle();

        $this->manifest->delete();
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/../stubs/dto.stub';
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param string $name
     * @return string
     */
    protected function qualifyClass($name): string
    {
        return $this->type = $this->qualifier->qualify($name);
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput(): string
    {
        $model = str_replace('/', '\\', parent::getNameInput());

        if (is_subclass_of($model, Model::class)) {
            return $model;
        }

        throw new InvalidArgumentException("Invalid model [$model]");
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name): string
    {
        $this->manifest->addStartingDto($name)->addGeneratingDto($name)->save();

        $search = ['DummyModel', 'DummyProperties', 'DummyUseStatements'];
        $replace = [class_basename($this->getNameInput()), $this->getModelProperties(), $this->getUseStatements()];
        $content = parent::buildClass($name);

        return $this->sortUseStatements(str_replace($search, $replace, $content));
    }

    /**
     * Retrieve the given model properties
     *
     * @return string
     */
    protected function getModelProperties(): string
    {
        $properties = '';
        $map = $this->laravel->make(ModelPropertiesMapper::class)->map($this->getCommandDto());
        $this->type = $this->manifest->getGeneratingDto();

        foreach ($map as $name => $types) {
            foreach ($types as &$type) {
                $normalizedType = rtrim($type, '[]');

                if ($this->shouldBeAddedToUseStatements($normalizedType)) {
                    $this->useStatements[$normalizedType] = true;
                }

                $type = class_basename($type);
            }

            $type = implode('|', $types);
            $properties .= " * @property {$type} \${$name}\n";
        }

        return rtrim($properties);
    }

    /**
     * Retrieve the DTO generation data
     *
     * @return DtoGenerationData
     */
    protected function getCommandDto(): DtoGenerationData
    {
        return DtoGenerationData::make([
            'model_class' => $model = $this->getNameInput(),
            'model' => new $model(),
            'forced' => $this->option('force') ?: false,
            'output' => $this->getOutput(),
        ]);
    }

    /**
     * Determine whether the given type should be added to use statements
     *
     * @param string $type
     * @return bool
     */
    protected function shouldBeAddedToUseStatements(string $type): bool
    {
        if ($this->hasSameNamespace($type)) {
            return false;
        }

        $usesStartingDto = $this->manifest->isStartingDto($type) && !$this->manifest->isGenerating($type);
        $generatedOrGenerating = $this->manifest->generated($type) || $this->manifest->generating($type);

        return $usesStartingDto || $generatedOrGenerating || class_exists($type);
    }

    /**
     * Determine whether the given class has the same namespace of the DTO that is going to be generated
     *
     * @param string $class
     * @return bool
     */
    protected function hasSameNamespace(string $class): bool
    {
        $segmentsClass = explode('\\', $class);
        $segmentsDto = explode('\\', $this->manifest->getGeneratingDto());

        array_pop($segmentsClass);
        array_pop($segmentsDto);

        return $segmentsClass === $segmentsDto;
    }

    /**
     * Retrieve the use statements
     *
     * @return string|null
     */
    protected function getUseStatements(): ?string
    {
        $useStatements = '';

        foreach ($this->useStatements as $class => $value) {
            $useStatements .= "\nuse {$class};";
            unset($this->useStatements[$class]);
        }

        return $useStatements;
    }

    /**
     * Previous versions of Laravel didn't sort imports.
     * Overriding this method and using sortUseStatements() instead keeps generated DTOs consistent across all versions
     *
     * @param string $stub
     * @return string
     */
    protected function sortImports($stub)
    {
        return $stub;
    }

    /**
     * Alphabetically sorts the use statements for the given stub
     *
     * @param string $stub
     * @return string
     */
    protected function sortUseStatements($stub)
    {
        preg_match('/(?P<imports>(?:use [^;]+;$\n?)+)/m', $stub, $match);

        $imports = explode("\n", trim($match['imports']));

        sort($imports);

        return str_replace(trim($match['imports']), implode("\n", $imports), $stub);
    }
}
