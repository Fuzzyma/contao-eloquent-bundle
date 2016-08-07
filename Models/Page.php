<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 16.07.16 01:09
 * @package: vlipgo
 */

namespace Fuzzyma\Contao\EloquentBundle\Models;
use Fuzzyma\Contao\EloquentBundle\Traits\PidScopeTrait;
use Fuzzyma\Contao\EloquentBundle\Traits\PublishedScopeTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
//use Illuminate\Database\Eloquent\Builder;

/**
 * Class Page
 * @package Fuzzyma\Contao\EloquentBundle\Models
 *
 * Examples:
 * old                                                  new
 * PageModel::findPublishedById                         Page::published()->find($id)
 * PageModel::findFirstPublishedByPid                   Page::published()->pid($pid)->first()
 * PageModel::findFirstPublishedRootByHostAndLanguage   Page::published()->root()->host($host)->language($languages)->first()
 * PageModel::findFirstPublishedByPid                   Page::published()->pid($pid)->first()
 * PageModel::findFirstPublishedRegularByPid            Page::published()->regular()->pid($pid)->first()
 * PageModel::find403ByPid                              Page::published()->forbidden()->pid($pid)->first()
 * PageModel::find404ByPid                              Page::published()->notFound()->pid($pid)->first()
 * PageModel::findByAliases                             Page::published()->where('alias', $aliases)->get()
 * PageModel::findPublishedByIdOrAlias                  Page::published()->idOrAlias($idOrAlias)->get()
 * PageModel::findPublishedSubpagesWithoutGuestsByPid   Page::published()->pid($pid)->noGuests()->get()
 * PageModel::findPublishedRegularWithoutGuestsByIds    Page::published()->regular()->noGuest()->find($ids)
 * PageModel::findPublishedRegularWithoutGuestsByPid    Page::published()->regular()->noGuest()->pid($pid)->get()
 * PageModel::findPublishedFallbackByHostname           Page::published()->host($host)->where('fallback', 1)->first()
 * PageModel::findPublishedRootPages                    Page::published()->root()->get()
 * PageModel::findParentsById                           Page::find($id)->parents()
 * PageModel::findFirstActiveByMemberGroups
 *
 */
class Page extends Model{

    use PublishedScopeTrait;
    use PidScopeTrait;

    public function hostScope(Builder $query, $host){
        return $query->where('dns', $host)->orWhere('dns')->orderBy('dns', 'DESC');
    }

    public function languageScope(Builder $query, $language)
    {
        if (!is_array($language)) $language = [$language];

        $query = $query->where('fallback', 1)->orWhereIn('language', $language);
        $query = $query->orderByRaw('FIND_IN_SET("language", ?) DESC', implode(',',array_reverse($language)));

        return $query->orderBy('sorting', 'ASC');
    }

    public function rootScope(Builder $query){
        return $query->where('type', 'root');
    }

    public function regularScope(Builder $query){
        return $query->where('type', 'regular');
    }

    public function forbiddenScope(Builder $query){
        return $query->where('type', '403');
    }

    public function notFoundScope(Builder $query){
        return $query->where('type', '404');
    }

    public function noGuestScope(Builder $query){
        return $query->where('guests', '');
    }

    public function parent(){
        $this->belongsTo(Page::class, 'pid');
    }

    public function parents(){
        $parents = new Collection();
        $parent = $this;
        while($parent = $parent->parent){
            $parents->add($parent);
        }
        return $parents;
    }

    public function subpages(){
        $this->hasMany(Page::class, 'pid');
    }


}