<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 13.07.16 01:24
 * @package: vlipgo
 */

namespace Fuzzyma\Contao\EloquentBundle\Models;


use Fuzzyma\Contao\EloquentBundle\Traits\PidScopeTrait;
use Fuzzyma\Contao\EloquentBundle\Traits\VisibleScopeTrait;


/**
 * Class ImageSizeItem
 * @package Fuzzyma\Contao\EloquentBundle\Models
 *
 * Examples
 * old                                    new
 * ImageSizeItemModel::findVisibleByPid   ImageSizeItem::visible()->pid($pid)->get()
 */
class ImageSizeItem extends Model{

    use PidScopeTrait;
    use VisibleScopeTrait;

} 