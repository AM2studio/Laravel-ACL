<?php

namespace AM2Studio\LaravelACL\Traits;

use Illuminate\Support\Str;

/**
 * Class Slugable
 * @package AM2Studio\LaravelACL\Traits
 */
trait Slugable
{
    /**
     * @param $value
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value, config('acl.separator'));
    }
}
