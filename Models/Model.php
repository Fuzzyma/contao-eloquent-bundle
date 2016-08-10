<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 12.07.16 23:49
 * @package: vlipgo
 */

namespace Fuzzyma\Contao\EloquentBundle\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Model
 * @package Fuzzyma\Contao\EloquentBundle\Models
 */
class Model extends Eloquent{

    protected $guarded = [];
    public $timestamps = false;

    public function scopeIdOrAlias(Builder $query, $idOrAlias){
        return $query->where('id', $idOrAlias)->orWhere('alias', $idOrAlias);
    }

} 