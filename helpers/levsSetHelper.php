<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-27 16:34
 *
 * 项目：upload  -  $  - setHelper.php
 *
 * 作者：liwei 
 */

namespace modules\levs\helpers;

use Lev;
use lev\helpers\ModulesHelper;
use lev\helpers\SettingsHelper;

!defined('INLEV') && exit('Access Denied LEV');

class BaseLevsSet {

    public static function setexceptsLev() {
        $res = ModulesHelper::findAllField('identifier,name', '1');
        $arr[0] = '空';
        $arr['__allLev'] = '<tips>所有Lev插件全例外</tips>';
        foreach ($res as $v) {
            $arr[$v['identifier']] = $v['name'];
        }
        return $arr;
    }

    public static function Icp() {
        return Lev::stget('Icp', 'levs');
    }

    public static function globalAdBtn() {
        return !Lev::stget('globalAdBtn', 'levs');
    }

    public static function qrcodeSrc() {
        return Lev::stget('qrcodeSrc', 'levs');
    }

    public static function appTitle() {
        return trim(Lev::stget('appTitle', 'levs'));
    }

    public static function coolTime() {
        return floatval(Lev::stget('coolTime', 'levs'));
    }

    public static function homeLink() {
        $link = trim(Lev::stget('homeLink', 'levs'));
        return $link ? Lev::toRoute([$link]) : '';
    }

    public static function autoTime() {
        $var = floatval(Lev::stget('autoTime', 'levs'));
        return $var <2 ? 2 : $var;
    }

    public static function openWelcome() {
        return !Lev::stget('openWelcome', 'levs');
    }

    public static function welcomeImgs() {
        return SettingsHelper::slidesFormat(Lev::stget('welcomeImgs', 'levs'));
    }

    public static function appLink() {
        return Lev::stget('appLink', 'levs');
    }
    public static $topLinks = [];
    public static function appTopLink() {
        return ['link'=>static::appLinkFormat(static::appLink()), 'top'=>static::$topLinks];
    }
    public static function appLinkFormat($navs, $lazy = true) {
        $navs && !is_array($navs) && $navs = unserialize($navs);
        $res = [];
        if ($navs) {
            $logoField = 'logoupload';
            foreach ($navs as $k => $v) {
                if (!$v['status']) {
                    $v['_icon'] = SettingsHelper::navIcon($v['id'], $v[$logoField], $lazy);
                    //$v['_target'] = SettingsHelper::navTarget($v['target']);
                    $v['link'] = Lev::toRoute([$v['link']]);
                    $v['_link'] = $v['link'] ? '" href="' . $v['link'] : '';
                    !$v['topstatus'] && static::$topLinks[$k] = $v;
                    !empty($v['cld__']) && $v['cld__'] = static::appLinkFormat($v['cld__'], $lazy);
                    $res[$k] = $v;
                }
            }
        }
        return $res;
    }

    public static function field_position() {
        return hookHelper::qrcodePosition();
    }

}

class levsSetHelper extends BaseLevsSet
{

}