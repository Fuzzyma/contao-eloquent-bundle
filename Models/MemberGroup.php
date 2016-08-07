<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 16.07.16 01:09
 * @package: vlipgo
 */

namespace Fuzzyma\Contao\EloquentBundle\Models;


use Fuzzyma\Contao\EloquentBundle\Traits\ActiveScopeTrait;


/**
 * Class MemberGroup
 * @package Fuzzyma\Contao\EloquentBundle\Models
 *
 * Examples:
 * old                                  new
 * MemberGroupModel::findPublishedById  MemberGroup::active()->find($id)
 * MemberGroupModel::findAllActive      MemberGroup::active()->get()
 */
class MemberGroup extends Model{

    use ActiveScopeTrait;

} 