<?php

namespace App\Traits;

use App\Models\Scopes\SiteScope;

trait HasSiteScope
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new SiteScope);
    }
}
