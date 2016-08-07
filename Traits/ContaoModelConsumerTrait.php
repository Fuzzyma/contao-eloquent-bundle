<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 09.07.16 14:18
 * @package: ContaoEloquentBundle
 */

namespace Fuzzyma\Contao\EloquentBundle\Traits;


use Contao\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait ContaoModelConsumerTrait
{

    // takes contao model or collection and turns it into an eloquent model/collection
    public static function consume($model)
    {
        if ($model instanceof Model) {
            return new static($model->row());
        } elseif ($model instanceof Model\Collection) {
            return static::hydrate($model->fetchAll());
        } else {
            throw new ModelNotFoundException('Given data was neither model or collection from contao');
        }
    }

}