<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 09.07.16 14:13
 * @package: ContaoEloquentBundle
 */

namespace Fuzzyma\Contao\EloquentBundle\Traits;


use Contao\Date;
use Illuminate\Database\Eloquent\Builder;

trait PublishedScopeTrait
{

    public function scopePublished(Builder $query, $ignoreFePreview = false)
    {
        // mimic behavior of contao models who never apply the pusblished filter when Backenduser is logged in
        if($ignoreFePreview || BE_USER_LOGGED_IN){
            return $query;
        }

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