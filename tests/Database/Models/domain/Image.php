<?php

namespace App\Images;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    /**
     * Relationships morphing to this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}
