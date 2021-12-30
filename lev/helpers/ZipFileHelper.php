<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-10-15 23:49
 *
 * 项目：rm  -  $  - ZipFileHelper.php
 *
 * 作者：liwei 
 */

namespace lev\helpers;

use Lev;
use ZipArchive;

!defined('INLEV') && exit('Access Denied LEV');


class ZipFileHelper
{

    public static $saveDir = '/zips';

    public static function setZipSaveDir($dir = '/') {
        cacheFileHelpers::mkdirv($dir = Lev::$aliases['@runtime'] . static::$saveDir . $dir) && cacheFileHelpers::setc('zipsdir', $dir);
        return $dir;
    }

    /**
     * @param $filesrc
     * @return bool
     */
    public static function unZipModules($filesrc) {
        return static::unZip($filesrc, Lev::$aliases['@modules']);
    }

    public static function unZipMudREADME($iden, $zipsrc) {
        cacheFileHelpers::mkdirv($dir = dirname(ModulesHelper::getREADMEdir($iden)));
        return static::unZip($zipsrc, $dir);
    }

    public static function unZip($filesrc, $toDir) {

        $zip = new ZipArchive();
        if ($zip->open($filesrc) === true) {
            $zip->extractTo($toDir);
            $zip->close();
            return true;
        }
        return false;
    }

}