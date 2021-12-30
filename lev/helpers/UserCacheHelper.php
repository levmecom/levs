<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-11-01 13:38
 *
 * 项目：rm  -  $  - UserCacheHelper.php
 *
 * 作者：liwei 
 */

namespace lev\helpers;

!defined('INLEV') && exit('Access Denied LEV');


class baseUserCache extends cacheFileHelpers
{

    public static $cacheDir = '/user';


    public static function getUserCacheKey($uid, $key)
    {
        return ($uid%900) . '/' . ($uid%800) . '/' . ($uid%700) . '/' . ($uid%500) . '/' . $uid . '/'.$key;
    }

    public static function setUserSetting($uid, $key, $value, $timeout = 0) {
        $ckey = static::getUserCacheKey($uid, $key);
        return static::setc($ckey, $value, $timeout);
    }

    public static function getUserSetting($uid, $key) {
        return static::getc(static::getUserCacheKey($uid, $key), false);
    }

    public static function clearUserSettings($uid, $key, $dir = false) {
        $ckey = static::getUserCacheKey($uid, $key);
        return static::clearc($ckey, $dir);
    }
}


class UserCacheHelper extends baseUserCache {

}
