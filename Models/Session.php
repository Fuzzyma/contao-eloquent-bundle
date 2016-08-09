<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 16.07.16 01:09
 * @package: vlipgo
 */

namespace Fuzzyma\Contao\EloquentBundle\Models;

use Fuzzyma\Contao\EloquentBundle\Traits\PidScopeTrait;
use Illuminate\Database\Query\Builder;


/**
 * Class Session
 * @package Fuzzyma\Contao\EloquentBundle\Models
 *
 * Examples:
 * old                                new
 * SessionModel::findByHashAndName    Session::hash($hash)->name($name)
 */
class Session extends Model{

    use PidScopeTrait;

    public function hashScope(Builder $query, $hash){
        return $query->where('hash', $hash);
    }

    public function nameScope(Builder $query, $name){
        return $query->where('name', $name);
    }

}