<?php

namespace Cerbero\LaravelDto;

use Cerbero\LaravelDto\Console\DefaultDtoQualifier;
use Cerbero\LaravelDto\Console\DtoGenerationData;
use Cerbero\LaravelDto\Console\Manifest;
use Cerbero\LaravelDto\Console\ModelPropertiesMapper;
use PHPUnit\Framework\TestCase;

use const Cerbero\Dto\PARTIAL;

/**
 * Tests for ModelPropertiesMapper.
 *
 */
class ModelPropertiesMapperTest extends TestCase
{
    /**
     * @test
     */
    public function skips_models_that_cannot_be_qualified()
    {
        $manifest = new Manifest(__DIR__ . '/manifest.php');
        $mapper = new ModelPropertiesMapper($manifest, new DefaultDtoQualifier);
        $data = DtoGenerationData::make(['model' => new TestModel], PARTIAL);

        $this->assertEmpty($mapper->mapPropertiesFromRelations($data));

        $manifest->delete();
    }
}
