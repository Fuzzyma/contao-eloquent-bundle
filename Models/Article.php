<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 13.07.16 00:03
 * @package: vlipgo
 */

namespace Fuzzyma\Contao\EloquentBundle\Models;


use Fuzzyma\Contao\EloquentBundle\Traits\PidScopeTrait;
use Fuzzyma\Contao\EloquentBundle\Traits\PublishedScopeTrait;
use Fuzzyma\Contao\EloquentBundle\Traits\SortedTrait;
use Illuminate\Database\Eloquent\Builder;


/**
 * Class Article
 * @package Fuzzyma\Contao\EloquentBundle\Models
 *
 * Examples:
 * old                                                  new
 * ArticleModel::findByIdOrAliasAndPid                  Article::idOrAlias($idOrAlias)->pid($pid)->first()
 * ArticleModel::findPublishedByIdOrAliasAndPid         Article::published()->idOrAlias($idOrAlias)->pid($pid)->first()
 * ArticleModel::findPublishedById                      Article::published()->find($id)
 * ArticleModel::findPublishedByPidAndColumn            Article::published()->pid($pid)->where($arr)->get()
 * ArticleModel::findPublishedWithTeaserByPid           Article::published()->withTeaser()->pid($pid)->get()
 * ArticleModel::findPublishedWithTeaserByPidAndColumn  Article::published()->withTeaser()->pid($pid)->where($arr)->get()
 *
 * wow - that's ridiculous
 */
class Article extends Model{

    protected $table = 'article';

    use PublishedScopeTrait;
    use SortedTrait;
    use PidScopeTrait;

    public function publishedByIdOrAliasAndPid(Builder $query, $idOrAlias, $pid){
        return $query->published()->idOrAlias($idOrAlias)->pid($pid);
    }

    public function scopeWithTeaser(Builder $query){
        return $query->where('showTeaser', 1);
    }
}