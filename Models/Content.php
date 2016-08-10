<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 13.07.16 00:38
 * @package: vlipgo
 */

namespace Fuzzyma\Contao\EloquentBundle\Models;

use Fuzzyma\Contao\EloquentBundle\Traits\PidScopeTrait;
use Fuzzyma\Contao\EloquentBundle\Traits\PublishedScopeTrait;
use Fuzzyma\Contao\EloquentBundle\Traits\SortedTrait;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Content
 * @package Fuzzyma\Contao\EloquentBundle\Models
 *
 * Examples
 * old                                          new
 * ContentModel::findPublishedByPidAndTable     Content::published()->pid($pid)->table($table)->get()
 * ContentModel::countPublishedByPidAndTable    Content::published()->pid($pid)->table($table)->count()
 */
class Content extends Model{

    protected $table = 'content';

    use PublishedScopeTrait;
    use SortedTrait;
    use PidScopeTrait;

    public function scopeTable(Builder $query, $table){
        return $query->where('ptable', $table)->orWhere('ptable', '');
    }

} 