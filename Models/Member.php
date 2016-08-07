<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 16.07.16 01:09
 * @package: vlipgo
 */

namespace Fuzzyma\Contao\EloquentBundle\Models;


use Fuzzyma\Contao\EloquentBundle\Traits\ActiveScopeTrait;
use Illuminate\Database\Query\Builder;


/**
 * Class Member
 * @package Fuzzyma\Contao\EloquentBundle\Models
 *
 * Examples:
 * old                                          new
 * MemberModel::findActiveByEmailAndUsername    Member::active()->email($email)->username($username)->first()
 * MemberModel::findUnactivatedByEmail          Member::unactivated($email)->first()
 */
class Member extends Model{

    use ActiveScopeTrait;

    public function emailScope(Builder $query, $email){
        return $query->where('email', $email)->where('login', 1);
    }

    public function usernameScope(Builder $query, $username){
        return $query->where('username', $username);
    }

    public function unactivatedScope(Builder $query, $email){
        return $query->where('email', $email)->where('activation', '!=');
    }

} 