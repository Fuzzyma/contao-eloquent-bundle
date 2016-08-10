<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 16.07.16 01:09
 * @package: vlipgo
 */

namespace Fuzzyma\Contao\EloquentBundle\Models;


use Contao\StyleSheetModel;
use Fuzzyma\Contao\EloquentBundle\Traits\ContaoModelConsumerTrait;

/**
 * Class StyleSheet
 * @package Fuzzyma\Contao\EloquentBundle\Models
 */
class StyleSheet extends Model{

    protected $table = 'style_sheet';

    use ContaoModelConsumerTrait;

    public function findByIds($ids){
        return self::consume(StyleSheetModel::findByIds($ids));
    }

}