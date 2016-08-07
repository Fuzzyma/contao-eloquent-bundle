<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 12.07.16 23:49
 * @package: vlipgo
 */

namespace Fuzzyma\Contao\EloquentBundle\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Query\Builder;

/**
 * Class Model
 * @package Fuzzyma\Contao\EloquentBundle\Models
 */
class Model extends Eloquent{

    private $guarded = [];
    private $timestamps = false;

    public function idOrAliasScope(Builder $query, $idOrAlias){
        return $query->where('id', $idOrAlias)->orWhere('alias', $idOrAlias);
    }

} 