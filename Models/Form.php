<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 16.07.16 00:43
 * @package: vlipgo
 */

namespace Fuzzyma\Contao\EloquentBundle\Models;


use Fuzzyma\Contao\EloquentBundle\Traits\VisibleScopeTrait;


/**
 * Class Form
 * @package Fuzzyma\Contao\EloquentBundle\Models
 */
class Form extends Model{

    protected $table = 'form';

    use VisibleScopeTrait;

    public function getMaxUploadFileSize(){

        $res = FormField::pid($this->id)->visible()->where('type', 'upload')->where('maxlength', '>', 0)->max('maxlength')->first();

        // this has to be tested
        dd($res);

        if($res && $res->maxlength > 0){
            return $res->maxlength;
        }else{
            return \Config::get('maxFileSize');
        }

    }

} 