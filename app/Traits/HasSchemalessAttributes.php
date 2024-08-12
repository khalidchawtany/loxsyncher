<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

trait HasSchemalessAttributes
{
    public function initializeHasSchemalessAttributes()
    {
        $this->casts['extra'] = SchemalessAttributes::class;
    }

    public function scopeWithExtraAttributes(): Builder
    {
        return $this->extra->modelScope();
    }
}
