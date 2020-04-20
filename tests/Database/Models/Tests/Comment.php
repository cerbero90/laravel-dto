<?php

namespace Cerbero\LaravelDto\Database\Models\Tests;

use Cerbero\LaravelDto\Traits\TurnsIntoDto;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use TurnsIntoDto;

    /**
     * Relationship with the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with the Post model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
