<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 13.07.16 00:47
 * @package: vlipgo
 */

namespace Fuzzyma\Contao\EloquentBundle\Models;

use Contao\StringUtil;
use Contao\Validator;
use Illuminate\Database\Query\Builder;

/**
 * Class Files
 * @package Fuzzyma\Contao\EloquentBundle\Models
 *
 * Examples
 * old                                          new
 * FilesModel::findByUuid                       Files::uuid($uuid)->first()
 * FilesModel::findMultipleByUuid               Files::uuid($uuids)->get()
 * FilesModel::findMultipleByPaths              Files::paths($paths)->get()
 * FilesModel::findMultipleByBasepath           Files::basePath($paths)->get()
 * FilesModel::findMultipleByUuidsAndExtensions Files::uuid($uuids)->extension($extensions)->get()
 * FilesModel::findMultipleFilesByFolder        Files::folder($folder)
 */
class Files extends Model{

    public function uuidScope(Builder $query, $uuid){
        if(is_array($uuid)){
            $uuid = array_map(function($uuid){
                return Validator::isStringUuid($uuid) ? StringUtil::uuidToBin($uuid) : $uuid;
            }, $uuid);
            return $query->whereIn('uuid', $uuid);
        }

        if (Validator::isStringUuid($uuid))
        {
            $uuid = StringUtil::uuidToBin($uuid);
        }
        return $query->where('uuid', $uuid);
    }

    public function pathScope(Builder $query, Array $paths){
        return $query->whereIn('paths', $paths)->orderByRaw('FIND_IN_SET(paths, ?)', implode(',', $paths));
    }

    public function basePathScope(Builder $query, Array $paths){
        return $query->where('paths','LIKE', $paths);
    }

    public function extensionScope(Builder $query, Array $extension){
        return $query->whereIn('extension',$extension);
    }

    public function folderScope(Builder $query, Array $folder){
        $strPath = str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $folder);

        return $query->where('type','file')->where('path', 'LIKE', $folder.'/%')->where('path', 'NOT LIKE', $folder.'/%/%');
    }

} 