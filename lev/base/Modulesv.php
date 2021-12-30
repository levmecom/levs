<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-23 10:01
 *
 * 项目：upload  -  $  - Modulesv.php
 *
 * 作者：liwei 
 */

namespace lev\base;

use Lev;
use lev\helpers\cacheFileHelpers;
use lev\helpers\ModulesHelper;

!defined('INLEV') && exit('Access Denied LEV');


class BaseModulesv
{

    public static function init() {

    }

    public static function isInstallModule($iden) {
        return is_file(Lev::$aliases['@settings'].'/modules/'.$iden.'.php');
    }
    public static function getModuleFileInfo($iden) {
        static $infos;
        !isset($infos[$iden]) && $infos[$iden] =
            is_file($paramsFile = Lev::$aliases['@settings'].'/modules/'.$iden.'.php') ? include $paramsFile : [];
        return $infos[$iden];
    }

    public static function getIdenNs($iden, $classdir = false) {
        return str_replace("/", "\\", static::getIdenDir($iden, $classdir));
    }

    public static function getIdenDir($iden, $classdir = false) {
        if ($classdir === false) {
            $mudInfo = static::getModuleFileInfo($iden);
            $classdir = $mudInfo['classdir'];
        }
        if ($classdir) {
            return $classdir.'/modules/'.$iden;
        }
        return $iden;
    }

    public static function getIdenRouteId($iden, $classdir = false) {
        if ($iden && $classdir === false && strpos($iden, ':') === false) {
            $mudInfo = static::getModuleFileInfo($iden);
            $classdir = $mudInfo['classdir'];
        }
        if ($classdir) {
            return $classdir.':'.$iden;
        }
        return $iden;
    }

    /**
     * 获取未安装组件
     * @return array
     */
    public static function getInstallModules() {
        $idenDir = Lev::getAlias('@modules/'.Lev::$app['iden']);
        $lists = glob($idenDir.'/modules/*/');
        $arr = [];
        if ($lists) {
            foreach ($lists as $v) {
                if ((!static::isInstallModule($iiden = basename($v)) || !is_file($idenDir.'/'.$iiden.'.inc.php')) &&
                    is_file(static::getRouteFile($iiden, Lev::$app['iden'])) &&
                    $v = Lev::actionObjectMethod('modules\\'.static::getIdenNs($iiden, Lev::$app['iden']).'\migrations\_install', [], 'getModuleInfo')) {
                    $arr[] = $v;
                }
            }
        }
        return $arr;
    }
    public static function getRouteFile($iden, $classdir) {
        return Lev::getAlias('@modules/'.ModulesHelper::getIdenDir($iden, $classdir).'/migrations/data/'.$iden.'.inc.php');
    }

    public static function createModuleFile($mudInfo) {
        $size = null;
        if (!empty($mudInfo) && is_array($mudInfo)) {
            $mudInfo['settings'] = Lev::getSettings($mudInfo['settings']);
            $mudInfo['settings']['_adminNavs'] = Lev::getSettings($mudInfo['settings']['_adminNavs']);
            $mudInfo['settings']['_adminClassify'] = Lev::getSettings($mudInfo['settings']['_adminClassify']);
            $mudInfo['idenDir'] = static::getIdenDir($mudInfo['identifier'], $mudInfo['classdir']);
            unset($mudInfo['settings']['dropTables']);
            //unset($mudInfo['settings']['dropTables'], $mudInfo['addtime'], $mudInfo['uptime'], $mudInfo['id'], $mudInfo['copyright']);

            $inLev = '!defined(\'INLEV\') && exit(\'Access Denied LEV\');'."\n";
            $paramsFile = Lev::getAlias('@settings/modules');
            cacheFileHelpers::mkdirv($paramsFile);
            $size = file_put_contents($paramsFile.'/'.$mudInfo['identifier'].'.php', '<?php '.$inLev.' return ' . var_export($mudInfo, true) . ';');
            if (!$size) {
                throw new \Exception('文件写入失败，请确定目录可写：'.$paramsFile);
            }
        }
        return $size;
    }

    public static function deleteModuleFile($iden, $force = false, $classdir = false) {
        if ($classdir === false) {
            $mudInfo = Modulesv::getModuleFileInfo($iden);
            $classdir = empty($mudInfo['classdir']) ? '' : $mudInfo['classdir'];
        }
        is_file($paramsFile = Lev::getAlias('@settings/modules/'.$iden.'.php')) && unlink($paramsFile);
        is_file($paramsFile = Lev::getAlias('@settings/'.$iden.'.php')) && unlink($paramsFile);
        $classdir &&
        is_file($paramsFile = Lev::getAlias('@modules/'.$classdir.'/'.$iden.'.inc.php')) && unlink($paramsFile);

        if (!$force && Lev::isDeveloper($iden)) return;
        static::deleteModuleDir(ModulesHelper::getIdenDir($iden, $classdir));
    }

    public static function deleteModuleDir($idenDir) {
        is_dir($dir = Lev::getAlias('@modules/'.$idenDir)) &&
        cacheFileHelpers::rmdirv($dir);
    }
}

class Modulesv extends BaseModulesv {}