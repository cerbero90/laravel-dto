<?php

namespace Cerbero\LaravelDto\Database\Models\Tests;

use Cerbero\LaravelDto\Factories\DtoFactoryContract;
use Cerbero\LaravelDto\TestDtoFactory;
use Cerbero\LaravelDto\Traits\TurnsIntoDto;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use TurnsIntoDto;

    /**
     * Relationship with the Comment model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Relationship with the Image model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Relationship with the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function writer()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retrieve the DTO assembler
     *
     * @return DtoFactoryContract
     */
    protected function getDtoFactory(): DtoFactoryContract
    {
        return new TestDtoFactory;
    }
}
