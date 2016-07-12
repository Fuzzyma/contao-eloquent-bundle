<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 09.07.16 14:13
 * @package: ContaoEloquentBundle
 */

namespace Fuzzyma\Contao\EloquentBundle\Traits;

use Illuminate\Database\Query\Builder;

trait SortedTrait
{

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('sorting', function(Builder $query) {
            $query->orderBy('sorting', 'ASC');
        });
    }

} 