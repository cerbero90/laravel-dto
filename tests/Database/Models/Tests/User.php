<?php

namespace Cerbero\LaravelDto\Database\Models\Tests;

use Cerbero\LaravelDto\Traits\TurnsIntoDto;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use TurnsIntoDto;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Relationship with the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function friends()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relationship with the Post model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

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
     * Relationship with the Comment model through the Post model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function postComments()
    {
        return $this->hasManyThrough(Comment::class, Post::class);
    }

    /**
     * Relationship with the Image model through the Post model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function images()
    {
        return $this->hasManyThrough(Image::class, Post::class);
    }
}
