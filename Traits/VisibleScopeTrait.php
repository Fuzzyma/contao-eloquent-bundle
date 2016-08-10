<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 09.07.16 14:13
 * @package: ContaoEloquentBundle
 */

namespace Fuzzyma\Contao\EloquentBundle\Traits;


use Illuminate\Database\Eloquent\Builder;

trait VisibleScopeTrait
{

    public function scopeVisible(Builder $query)
    {
        return $query->where('hidden', '');
    }

} 