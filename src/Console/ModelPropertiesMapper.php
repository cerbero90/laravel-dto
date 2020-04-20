<?php

namespace Cerbero\LaravelDto\Console;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

/**
 * The model properties mapper.
 *
 */
class ModelPropertiesMapper
{
    /**
     * The cached model file.
     *
     * @var array
     */
    protected $cachedFile;

    /**
     * The map between schema and PHP types.
     *
     * @var array
     */
    protected $schemaTypesMap = [
        'guid'     => 'string',
        'boolean'  => 'bool',
        'datetime' => 'Carbon\Carbon',
        'string'   => 'string',
        'json'     => 'string',
        'integer'  => 'int',
        'date'     => 'Carbon\Carbon',
        'smallint' => 'int',
        'text'     => 'string',
        'decimal'  => 'float',
        'bigint'   => 'int',
    ];

    /**
     * The map determining whether a relation involves many models.
     *
     * @var array
     */
    protected $relationsMap = [
        'hasOne'         => false,
        'morphOne'       => false,
        'belongsTo'      => false,
        'morphTo'        => false,
        'hasMany'        => true,
        'hasManyThrough' => true,
        'morphMany'      => true,
        'belongsToMany'  => true,
        'morphToMany'    => true,
        'morphedByMany'  => true,
    ];

    /**
     * The manifest.
     *
     * @var Manifest
     */
    protected $manifest;

    /**
     * The DTO qualifier.
     *
     * @var DtoQualifierContract
     */
    protected $qualifier;

    /**
     * Instantiate the class.
     *
     * @param Manifest $manifest
     * @param DtoQualifierContract $qualifier
     */
    public function __construct(Manifest $manifest, DtoQualifierContract $qualifier)
    {
        $this->manifest = $manifest;
        $this->qualifier = $qualifier;
    }

    /**
     * Retrieve the properties map of the given data to generate
     *
     * @param DtoGenerationData $data
     * @return array
     */
    public function map(DtoGenerationData $data): array
    {
        $propertiesFromDatabase = $this->mapPropertiesFromDatabase($data);
        $propertiesFromRelations = $this->mapPropertiesFromRelations($data);

        return $propertiesFromDatabase + $propertiesFromRelations;
    }

    /**
     * Retrieve the given model properties from the database
     *
     * @param DtoGenerationData $data
     * @return array
     */
    public function mapPropertiesFromDatabase(DtoGenerationData $data): array
    {
        $properties = [];
        $table = $data->model->getTable();
        $connection = $data->model->getConnection();

        foreach (Schema::getColumnListing($table) as $column) {
            $camelColumn = Str::camel($column);
            $rawType = Schema::getColumnType($table, $column);
            $types = [$this->schemaTypesMap[$rawType]];

            if (!$connection->getDoctrineColumn($table, $column)->getNotnull()) {
                $types[] = 'null';
            }

            $properties[$camelColumn] = $types;
        }

        return $properties;
    }

    /**
     * Retrieve the given model properties from its relations
     *
     * @param DtoGenerationData $data
     * @return array
     */
    public function mapPropertiesFromRelations(DtoGenerationData $data): array
    {
        $properties = [];
        $relations = implode('|', array_keys($this->relationsMap));
        $reflection = new ReflectionClass($data->model);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if ($method->getFileName() != $reflection->getFileName()) {
                continue;
            }

            if (!preg_match("/\\\$this->($relations)\W+([\w\\\]+)/", $this->getMethodBody($method), $matches)) {
                continue;
            }

            [, $relation, $relatedModel] = $matches;

            if (!$qualifiedModel = $this->qualifyModel($relatedModel, $reflection)) {
                continue;
            }

            $dto = $this->getDtoForModelOrGenerate($qualifiedModel, $data);
            $type = $this->relationsMap[$relation] ? $dto . '[]' : $dto;
            $properties += [$method->getName() => [$type]];
        }

        return $properties;
    }

    /**
     * Retrieve the body of the given method
     *
     * @param ReflectionMethod $method
     * @return string
     */
    protected function getMethodBody(ReflectionMethod $method): string
    {
        $file = $this->getFile($method->getFileName());
        $offset = $method->getStartLine();
        $length = $method->getEndLine() - $offset;

        return implode('', array_slice($file, $offset, $length));
    }

    /**
     * Retrieve the given file as an array
     *
     * @param string $filename
     * @return array
     */
    protected function getFile(string $filename): array
    {
        if ($this->cachedFile) {
            return $this->cachedFile;
        }

        return $this->cachedFile = file($filename);
    }

    /**
     * Retrieve the fully qualified class name of the given model
     *
     * @param string $model
     * @param ReflectionClass $reflection
     * @return string|null
     */
    protected function qualifyModel(string $model, ReflectionClass $reflection): ?string
    {
        if (class_exists($model)) {
            return $model;
        }

        $useStatements = $this->getUseStatements($reflection);

        if (isset($useStatements[$model])) {
            return $useStatements[$model];
        }

        return class_exists($class = $reflection->getNamespaceName() . "\\{$model}") ? $class : null;
    }

    /**
     * Retrieve the use statements of the given class
     *
     * @param ReflectionClass $reflection
     * @return array
     */
    protected function getUseStatements(ReflectionClass $reflection): array
    {
        $class = $reflection->getName();

        if ($useStatements = $this->manifest->getUseStatements($class)) {
            return $useStatements;
        }

        $file = $this->getFile($reflection->getFileName());

        foreach ($file as $line) {
            if (strpos($line, 'class') === 0) {
                break;
            } elseif (strpos($line, 'use') === 0) {
                preg_match_all('/([\w\\\_]+)(?:\s+as\s+([\w_]+))?;/i', $line, $matches, PREG_SET_ORDER);

                foreach ($matches as $match) {
                    $segments = explode('\\', $match[1]);
                    $name = $match[2] ?? end($segments);
                    $this->manifest->addUseStatement($class, $name, $match[1]);
                }
            }
        }

        return $this->manifest->save()->getUseStatements($class);
    }

    /**
     * Retrieve the DTO class name for the given model
     *
     * @param string $model
     * @param DtoGenerationData $data
     * @return string
     */
    protected function getDtoForModelOrGenerate(string $model, DtoGenerationData $data): string
    {
        if ($dto = $this->manifest->getDto($model)) {
            return $dto;
        }

        $dto = $this->qualifier->qualify($model);

        if ($this->shouldGenerateNestedDto($dto, $data->forced)) {
            Artisan::call('make:dto', [
                'name' => str_replace('\\', '/', $model),
                '--force' => $data->forced,
            ], $data->output);

            $this->manifest->finishGeneratingDto()->save();
        }

        return $this->manifest->addDto($model, $dto)->save()->getDto($model);
    }

    /**
     * Determine whether the given nested DTO should be generated
     *
     * @param string $dto
     * @param bool $forced
     * @return bool
     */
    protected function shouldGenerateNestedDto(string $dto, bool $forced): bool
    {
        if ($this->manifest->isStartingDto($dto) || $this->manifest->generating($dto)) {
            return false;
        }

        return $forced || !class_exists($dto);
    }
}
