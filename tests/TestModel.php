<?php

namespace Cerbero\LaravelDto;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for testing purposes.
 *
 */
class TestModel extends Model
{
    /**
     * Relationship with the Unknown model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unknownModels()
    {
        return $this->hasMany(Unknown::class);
    }
}
