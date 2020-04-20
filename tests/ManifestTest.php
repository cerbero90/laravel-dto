<?php

namespace Cerbero\LaravelDto;

use Cerbero\LaravelDto\Console\Manifest;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Manifest.
 *
 */
class ManifestTest extends TestCase
{
    /**
     * @test
     */
    public function can_be_read_and_deleted()
    {
        $path = __DIR__ . '/manifest.php';
        $manifest = new Manifest($path);

        $this->assertFalse(file_exists($path));

        $manifest->read();

        $this->assertTrue(file_exists($path));
        $this->assertNull($manifest->getGeneratingDto());

        $manifest->delete();

        $this->assertFalse(file_exists($path));
    }
}
