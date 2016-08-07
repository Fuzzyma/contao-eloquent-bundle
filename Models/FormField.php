<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 13.07.16 01:24
 * @package: vlipgo
 */

namespace Fuzzyma\Contao\EloquentBundle\Models;


use Fuzzyma\Contao\EloquentBundle\Traits\PidScopeTrait;
use Fuzzyma\Contao\EloquentBundle\Traits\PublishedScopeTrait;
use Fuzzyma\Contao\EloquentBundle\Traits\SortedTrait;
use Fuzzyma\Contao\EloquentBundle\Traits\VisibleScopeTrait;


/**
 * Class FormField
 * @package Fuzzyma\Contao\EloquentBundle\Models
 *
 * Examples
 * old                                  new
 * FormFieldModel::findPublishedByPid   FormField::published()->pid($pid)->get()
 */
class FormField extends Model{

    use PublishedScopeTrait;
    use PidScopeTrait;
    use SortedTrait;
    use VisibleScopeTrait{
        scopeVisible as ignoreFePreviewScope;
    }

    /*
		if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN)
		{
			$arrColumns[] = "$t.invisible=''";
		}
    */

} 