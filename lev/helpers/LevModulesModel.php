<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-07 22:45
 *
 * 项目：upload  -  $  - LevModulesModel.php
 *
 * 作者：liwei 
 */

namespace lev\helpers;

use Lev;
use lev\base\Modelv;
use lev\base\Modulesv;
use modules\levs\modules\ftp\helpers\ftpZipHelper;

!defined('INLEV') && exit('Access Denied LEV');


class LevModulesModel extends Modelv
{

    public static function tableName($val = '', $prefix = true) {
        return parent::tableName($val ?: '{{%lev_modules}}', $prefix);
    }

    public static function getModuleInfo($iden) {
        return static::findOne(['identifier'=>$iden]);
    }

    public static function isOpenModule($iden) {
        return ($mud = static::getModuleFileInfo($iden)) && empty($mud['status']);
    }

    public static function isInstallModule($iden) {
        return Modulesv::isInstallModule($iden);
    }
    public static function getModuleFileInfo($iden) {
        return Modulesv::getModuleFileInfo($iden);
    }
    public static function getIdenDir($iden, $classdir = false) {
        return Modulesv::getIdenDir($iden, $classdir);
    }
    public static function checkNewConfig($mudInfo, $iden = '') {
        empty($mudInfo) && $iden && $mudInfo = static::getModuleInfo($iden);
        if (empty($mudInfo)) {
            return false;
        }
        $iden = $mudInfo['identifier'];
        if (static::getUpdateMuds($iden)) {
            return true;
        }
        //$fileMud = Lev::actionObjectMethod('modules\\'.Modulesv::getIdenNs($mudInfo['identifier']).'\migrations\_install', [], 'getModuleInfo');
        if ($fileMud = static::getMigrationsModuleInfo($iden)) {
            if ($fileMud['version'] > $mudInfo['version'] || $fileMud['versiontime'] > $mudInfo['versiontime']) {
                return true;
            }
        }
        return false;
    }

    public static function getMigrationsModuleInfo($iden) {
        return is_file($file = Lev::$aliases['@modules'] . '/' . static::getIdenDir($iden) . '/migrations/data/moduleinfo.php')
            ? include $file : [];
    }
    public static function checkUpdateFile($iden, $classdir = false) {
        return is_file(Lev::$aliases['@modules'] . '/' . static::getIdenDir($iden, $classdir) . '/migrations/_update.php');
    }

    public static function getUpdateMuds($iden = null) {
        static $arr;
        if (!isset($arr)) {
            $arr = [];
            static::isInstallModule('ftp') && ($arr = ftpZipHelper::opCache() ?: []);
        }
        return ($iden === null ? $arr : (isset($arr[$iden]) ? $arr[$iden] : null));
    }

    public static function getAdminSubnavHtmsAndBox() {
        return '<div class="subnavbar"><div class="mud-navb buttons-row scale8 transl">'.static::getAdminSubnavHtms().'</div></div>';
    }
    public static function getAdminSubnavHtms() {
        $htms = '';
        $iden = Lev::stripTags(Lev::GPv('iden')) ?: Lev::$app['iden'];
        $mudInfo = static::getModuleFileInfo($iden);
        $setNav = static::getClassify($iden, 1);
        $classify = Lev::GETv('classify');
        foreach ($setNav as $key => $name) {
            $color = $key == $classify ? ' color-blue' : ' color-gray';
            $href = Lev::toReRoute(['superman/settings', 'id'=>APPVIDEN, 'iden'=>$iden, 'classify'=>$key]);
            $htms.= '<a class="button-fill button wd80 wdmin'.$color.'" href="'.$href.'">'.$name.'</a>';
        }
        $classify = Lev::GETv('r');
        $setNav = static::getAdminNavs($mudInfo);
        $isShow = 0;
        foreach ($setNav as $v) {
            $pmr = explode("&", $v['pm'])[0];
            $color = ' color-blackg';
            if (!$isShow) {
                strpos($classify, $pmr) === 0 && $color = ' color-blue';
                ($isShow = $classify == $pmr) &&
                $htms = str_replace(' color-blue==', ' color-blackg', $htms);
            }
            $htms.= '<a class="button-fill button wd80 wdmin'.$color.$v['_link'].'">'.$v['name'].'</a>';
        }
        return $htms;
    }

    public static function getAdminNavHtms($mudInfo) {
        $htms = '';
        $setNav = static::getClassify($mudInfo['identifier'], 1);
        foreach ($setNav as $key => $name) {
            $href = Lev::toReRoute(['superman/settings', 'id'=>APPVIDEN, 'iden'=>$mudInfo['identifier'], 'classify'=>$key]);
            $htms.= '<a class="button-fill button wd60 wdmin color-gray" href="'.$href.'">'.$name.'</a>';
        }
        $setNav = static::getAdminNavs($mudInfo);
        foreach ($setNav as $v) {
            $v['target'] == 1 && $v['_target'] = '';
            $htms.= '<a class="button-fill button wd60 wdmin color-blackg '.$v['_target'].$v['_link'].'">'.$v['name'].'</a>';
        }
        return $htms . static::getAdminFormNavHtms($mudInfo);
    }

    public static function getAdminFormNavHtms($mudInfo) {
        $navs = static::getAdminFormNavs($mudInfo);
        $htms = '';
        if ($navs) {
            foreach ($navs as $v) {
                $htms .= '<a class="button-fill button wd60 wdmin color-black" href="'.$v['link'].'">' . $v['name'] . '</a>';
            }
        }
        return $htms;
    }

    /**
     * @param $iden
     * @param bool $status 为真时，只返回开启状态分类导航；否则返回所有
     * @return array
     */
    public static function getClassify($iden, $status = false) {
        $mudInfo = ModulesHelper::getModuleFileInfo($iden);
        $navs = Lev::getSettings($mudInfo['settings'], '_adminClassify');
        is_array($navs) || $navs = unserialize($navs);
        $arr = [];
        foreach ($navs as $v) {
            !($status && $v['status']) && $arr[$v['id']] = $v['name'];
        }
        return $arr;
    }
    public static function getSetClassify($iden, $classifyArr = [], $only = false) {
        $inputs = SettingsHelper::getModuleSettingsInfo($iden);

        $myOnly = [];
        foreach ($inputs as $v) {
            isset($classifyArr[$v['classify']]) ||
            $myOnly[$v['classify']] = $v['classify'];
        }
        return $only ? $myOnly : ($classifyArr + $myOnly);
    }

    public static function getAdminSetNavs($mudInfo) {
        $inputs = SettingsHelper::getModuleSettingsInfo($mudInfo['identifier']);

        $classifyArr = [];
        foreach ($inputs as $v) {
            $classifyArr[$v['classify']?:'default'][] = $v;
        }
        return $classifyArr;
    }
    public static function getAdminNavs($mudInfo) {
        $navs = Lev::getSettings($mudInfo['settings'], '_adminNavs');
        is_array($navs) || $navs = unserialize($navs);
        $res = [];
        if ($navs) {
            foreach ($navs as $k => $v) {
                if (!$v['status']) {
                    $v['_target'] = SettingsHelper::navTarget($v['target']);
                    $v['pm'] = $v['link'];
                    $v['link'] = Lev::toReRoute([$v['link'], 'id' => $mudInfo['identifier']]);
                    $v['_link'] = $v['link'] ? '" href="' . $v['link'] : '';
                    $res[$k] = $v;
                }
            }
        }
        return $res;
    }
    public static function getAdminFormNavs($mudInfo) {
        $navs = Lev::getSettings($mudInfo['settings'], '_forms');
        is_array($navs) || $navs = unserialize($navs);
        $res = [];
        if ($navs) {
            foreach ($navs as $k => $v) {
                if (!$v['status']) {
                    $route = Modelv::formRoute($v['formName'] ?: $v['formInputs']);
                    $v['link'] = Lev::toReRoute([$route, 'id' => $mudInfo['identifier']]);
                    $v['name'] = $v['title'] ?: $route;
                    $res[$k] = $v;
                }
            }
        }
        return $res;
    }


    public static function isErrorVersion($string, $inputname = 'version', $tipname = '版本号') {
        if (!preg_match('/^[a-zA-Z0-9_-][a-zA-Z0-9_.-]+$/', $string)) {
            return Modelv::errorMsg($inputname, $tipname.'只允许字母、数字、中划线和下划线', -2201);
        }
        return false;
    }
    public static function isErrorIden($iden, $inputname = 'identifier') {
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]+$/', $iden)) {
            return Modelv::errorMsg($inputname, '只允许a-z、0-9和下划线，必须以字母开头', -2000);
        }
        return false;
    }
    public static function isExistIden($idenDir) {
        return is_dir(Lev::getAlias('@modules/'.$idenDir));
    }

}