<?php

namespace Cerbero\LaravelDto\Console;

use Illuminate\Support\Arr;

/**
 * The DTOs generation manifest.
 *
 */
class Manifest
{
    /**
     * The manifest filename.
     *
     * @var string
     */
    protected $filename;

    /**
     * The manifest content.
     *
     * @var array
     */
    protected $manifest = [];

    /**
     * Instantiate the class.
     *
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * Write content in the manifest
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function write(string $key, $value): self
    {
        $this->manifest = $this->read();

        Arr::set($this->manifest, $key, $value);

        return $this;
    }

    /**
     * Retrieve the manifest content
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function read(string $key = null, $default = null)
    {
        if (!file_exists($this->filename)) {
            file_put_contents($this->filename, '<?php return [];');
        }

        if (empty($this->manifest)) {
            $this->manifest = require $this->filename;
        }

        return $key === null ? $this->manifest : Arr::get($this->manifest, $key, $default);
    }

    /**
     * Save the manifest
     *
     * @return self
     */
    public function save(): self
    {
        file_put_contents($this->filename, '<?php return ' . var_export($this->read(), true) . ';');

        return $this;
    }

    /**
     * Delete the manifest
     *
     * @return void
     */
    public function delete(): void
    {
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }

    /**
     * Add the given model DTO
     *
     * @param string $model
     * @param string $dto
     * @return self
     */
    public function addDto(string $model, string $dto): self
    {
        $this->write("{$model}.dto", $dto);

        return $this;
    }

    /**
     * Retrieve the given model DTO
     *
     * @param string $model
     * @return string|null
     */
    public function getDto(string $model): ?string
    {
        return $this->read("{$model}.dto");
    }

    /**
     * Add the given model use statements
     *
     * @param string $model
     * @param string $name
     * @param string $use
     * @return self
     */
    public function addUseStatement(string $model, string $name, string $use): self
    {
        $this->write("{$model}.use.{$name}", $use);

        return $this;
    }

    /**
     * Retrieve the given model use statements
     *
     * @param string $model
     * @return array
     */
    public function getUseStatements(string $model): array
    {
        return $this->read("{$model}.use", []);
    }

    /**
     * Add the given DTO that is being generated
     *
     * @param string $dto
     * @return self
     */
    public function addGeneratingDto(string $dto): self
    {
        $this->write("generating.{$dto}", true);

        return $this;
    }

    /**
     * Retrieve the DTO that is being generated now
     *
     * @return string|null
     */
    public function getGeneratingDto(): ?string
    {
        if (empty($generating = $this->read('generating'))) {
            return null;
        }

        $dtos = array_keys($generating);

        return end($dtos);
    }

    /**
     * Determine whether the given DTO is being generated now
     *
     * @param string $dto
     * @return bool
     */
    public function isGenerating(string $dto): bool
    {
        return $this->getGeneratingDto() === $dto;
    }

    /**
     * Determine whether the given DTO is in the process of being generated
     *
     * @param string $dto
     * @return bool
     */
    public function generating(string $dto): bool
    {
        return $this->read("generating.{$dto}") !== null;
    }

    /**
     * Mark the latest generating DTO as generated
     *
     * @return self
     */
    public function finishGeneratingDto(): self
    {
        if (!empty($this->read('generating'))) {
            $dto = $this->getGeneratingDto();
            $this->write("generated.{$dto}", true);
            array_pop($this->manifest['generating']);
        }

        return $this;
    }

    /**
     * Determine whether the given DTO has been generated
     *
     * @param string $dto
     * @return bool
     */
    public function generated(string $dto): bool
    {
        return $this->read("generated.{$dto}") !== null;
    }

    /**
     * Add the given class as starting DTO
     *
     * @param string $dto
     * @return self
     */
    public function addStartingDto(string $dto): self
    {
        if ($this->read('starting') === null) {
            $this->write('starting', $dto);
        }

        return $this;
    }

    /**
     * Retrieve the starting DTO
     *
     * @return string|null
     */
    public function getStartingDto(): ?string
    {
        return $this->read('starting');
    }

    /**
     * Determine whether the given class is the starting DTO
     *
     * @param string $dto
     * @return bool
     */
    public function isStartingDto(string $dto): bool
    {
        return $this->getStartingDto() === $dto;
    }
}
