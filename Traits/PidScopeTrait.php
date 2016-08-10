<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 09.07.16 14:13
 * @package: ContaoEloquentBundle
 */

namespace Fuzzyma\Contao\EloquentBundle\Traits;

use Illuminate\Database\Eloquent\Builder;

trait PidScopeTrait
{

    public function scopePid(Builder $query, $pid){
        return $query->where('pid', $pid);
    }

} 