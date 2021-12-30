<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-26 22:40
 *
 * 项目：upload  -  $  - User.php
 *
 * 作者：liwei 
 */

namespace lev\helpers;

use Lev;

!defined('INLEV') && exit('Access Denied LEV');


class UserHelper extends LevUserModel
{

    public static function checkEditPassword($uid) {
        $infos = static::findOne(['id'=>$uid]);
        return Lev::getSettings($infos['settings'], 'editPassword');
    }

    public static function updateSettingsEditPassword($uid, $num = 1) {
        $infos = static::findOne(['id'=>$uid]);
        $s = Lev::getSettings($infos['settings']);
        $s['editPassword'] = $num;
        static::update(['settings'=>Lev::setSettings($s)],['id'=>$uid]);
    }

    public static function checkEditUsername($uid) {
        $infos = static::findOne(['id'=>$uid]);
        return Lev::getSettings($infos['settings'], 'editUsername');
    }

    public static function updateSettingsEditUsernamePrice($uid, $num = 1) {
        $infos = static::findOne(['id'=>$uid]);
        $s = Lev::getSettings($infos['settings']);
        $s['editUsername'] = $num;
        static::update(['settings'=>Lev::setSettings($s)],['id'=>$uid]);
    }

}