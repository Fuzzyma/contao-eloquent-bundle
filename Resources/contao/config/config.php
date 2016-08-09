<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 07.07.16 16:18
 * @package: ContaoEloquentBundle
 */

$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array(Fuzzyma\Contao\EloquentBundle\Hooks\DCAEloquentifier::class, 'parseDCA');