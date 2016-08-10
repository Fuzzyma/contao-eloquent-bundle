<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 13.07.16 00:47
 * @package: vlipgo
 */

namespace Fuzzyma\Contao\EloquentBundle\Models;

use Contao\StringUtil;
use Contao\Validator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class File
 * @package Fuzzyma\Contao\EloquentBundle\Models
 *
 * Examples
 * old                                          new
 * FilesModel::findByUuid                       File::uuid($uuid)->first()
 * FilesModel::findMultipleByUuid               File::uuid($uuids)->get()
 * FilesModel::findMultipleByPaths              File::paths($paths)->get()
 * FilesModel::findMultipleByBasepath           File::basePath($paths)->get()
 * FilesModel::findMultipleByUuidsAndExtensions File::uuid($uuids)->extension($extensions)->get()
 * FilesModel::findMultipleFilesByFolder        File::folder($folder)
 */
class File extends Model{

    protected $table = 'files';

    public function scopeUuid(Builder $query, $uuid){
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

    public function scopePath(Builder $query, Array $paths){
        return $query->whereIn('paths', $paths)->orderByRaw('FIND_IN_SET(paths, ?)', implode(',', $paths));
    }

    public function scopeBasePath(Builder $query, Array $paths){
        return $query->where('paths','LIKE', $paths);
    }

    public function scopeExtension(Builder $query, Array $extension){
        return $query->whereIn('extension',$extension);
    }

    public function scopeFolder(Builder $query, Array $folder){
        $folder = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $folder);

        return $query->where('type','file')->where('path', 'LIKE', $folder.'/%')->where('path', 'NOT LIKE', $folder.'/%/%');
    }

} 