<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-10-26 11:47
 *
 * 项目：rm  -  $  - LevModulesREADME.php
 *
 * 作者：liwei 
 */

namespace lev\helpers;

use Lev;
use lev\base\Assetsv;

!defined('INLEV') && exit('Access Denied LEV');


class LevModulesREADME extends LevModulesModel
{

    /**
     * README.md 自述文件内容
     * @param $iden
     * @param bool $root
     * @return string
     */
    public static function getREADMEdir($iden, $root = true) {

        return Lev::$aliases[($root ? '@webroot' : '@web')] . '/data/README.md/'.$iden . '/README.md';
    }
    public static function getREADMElogo($iden, $root = false) {
        return static::getREADMEdir($iden, $root) . '/logo.png';
    }
    public static function getREADMEcover($iden, $root = false) {
        return static::getREADMEdir($iden, $root) . '/cover.jpg';
    }
    public static function getREADMEslides($iden, $root = false) {
        $dir = static::getREADMEdir($iden, true) . '/slides/';
        $files = glob($dir . '*.*');
        if ($files && !$root) {
            $web = static::getREADMEdir($iden, false) . '/slides/';
            foreach ($files as $k => $v) {
                if (is_file($v)) {
                    $files[$k] = $web . basename($v);
                }else {
                    unset($files[$k]);
                }
            }
        }
        return $files;
    }
    public static function getREADMEmd($iden) {
        return is_file($file = static::getREADMEdir($iden) . '/README.md') ? Lev::removeScript(file_get_contents($file)) : '';
    }

    public static function getREADMEimage($iden, $img = 'logo.png', $onlysrc = true) {
        $websrc = is_file($src = static::getREADMEdir($iden) . '/' .$img)
            ? static::getREADMEdir($iden, false) . '/' . $img
            : Assetsv::getAppwebassets() . '/caihod_logo.jpg';
        return $onlysrc ? $websrc :
            '<img data-src="'.$websrc.'" class="lazy">';
    }

}