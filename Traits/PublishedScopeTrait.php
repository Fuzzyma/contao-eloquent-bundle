<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 09.07.16 14:13
 * @package: ContaoEloquentBundle
 */

namespace Fuzzyma\Contao\EloquentBundle\Traits;


use Contao\Date;
use Illuminate\Database\Query\Builder;

trait PublishedScopeTrait
{

    public function scopePublished(Builder $query)
    {
        $time = Date::floorToMinute();
        return $query->where('published', 1)
            ->where(function(Builder $query) use ($time){
                return $query->where('start', '')->orWhere('start', '<=', $time);
            })
            ->where(function(Builder $query) use ($time){
                return $query->where('stop', '')->orWhere('stop', '>', $time+60);
            });

    }

} 