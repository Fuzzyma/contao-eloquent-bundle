<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 09.07.16 14:13
 * @package: ContaoEloquentBundle
 */

namespace Fuzzyma\Contao\EloquentBundle\Traits;


use Contao\Date;
use Illuminate\Database\Eloquent\Builder;

trait ActiveScopeTrait
{

    public function scopeActive(Builder $query)
    {
        $time = Date::floorToMinute();
        return $query->where('disable', '')
            ->where(function(Builder $query) use ($time){
                return $query->where('start', '')->orWhere('start', '<=', $time);
            })
            ->where(function(Builder $query) use ($time){
                return $query->where('stop', '')->orWhere('stop', '>', $time+60);
            });
    }

} 