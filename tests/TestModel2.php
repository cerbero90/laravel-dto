<?php

namespace Cerbero\LaravelDto;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for testing purposes.
 *
 */
class TestModel2 extends Model
{
    /**
     * Relationship with the Unknown model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function testModels()
    {
        return $this->hasMany('Cerbero\LaravelDto\TestModel');
    }
}
