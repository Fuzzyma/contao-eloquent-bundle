<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 09.07.16 14:13
 * @package: ContaoEloquentBundle
 */

namespace Fuzzyma\Contao\EloquentBundle\Traits;


trait PublishedScopeTrait
{

    public function scopePublished($query)
    {
        return $query->where('published', 1);
    }

} 