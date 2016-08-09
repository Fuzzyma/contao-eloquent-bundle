<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 07.07.16 11:39
 * @package: ContaoEloquentBundle
 */


namespace Fuzzyma\Contao\EloquentBundle\Traits;

use Contao\FilesModel;
use Contao\StringUtil;
use Contao\Validator;

trait ContaoFilesModelTrait
{

    // Always return a contao file model since its more suitable when working with contao
    private function getContaoFile($uuid)
    {
        $uuid = StringUtil::deserialize($uuid);

        if (is_array($uuid)) return FilesModel::findMultipleByUuids($uuid);
        return FilesModel::findByUuid($uuid);
    }

    // we check if its a file model and extract the uuid. If its a string we convert it.
    private function setContaoFile($file)
    {
        if (is_array($file)) {
            return serialize(array_map([__CLASS__, 'setContaoFile'], $file));
        }

        if ($file instanceof FilesModel) {
            return $file->uuid;
        }
        if (Validator::isStringUuid($file)) {
            return StringUtil::uuidToBin($file);
        }
        return $file;
    }
} 