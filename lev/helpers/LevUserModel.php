<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-26 22:39
 *
 * 项目：upload  -  $  - LevUserModel.php
 *
 * 作者：liwei 
 */

namespace lev\helpers;


use Lev;
use lev\base\Modelv;

!defined('INLEV') && exit('Access Denied LEV');

class LevUserModel extends Modelv
{
    public static $tableName = '{{%lev_users}}';

    /**
     * @param bool $prov
     * @return
     */
    public static function myCity($prov = false) {
        static $myDetail;
        !isset($myDetail) && $myDetail = static::getUserDetail(Lev::$app['uid']);
        return $prov ? $myDetail['prov'] : $myDetail['city'];
    }

    public static function getUserDetail($uid = 0) {
        return !$uid ? [] : static::findOne(['id'=>$uid], 'id as uid,rname,prov,city,qq');
    }

    public static function doLogin($username, $password, $autologin, $questionid = '', $answer = '', $ip = '') {
        $res = Lev::getDB()->doLogin($username, $password, $autologin, $questionid, $answer, $ip);
        if (empty(Lev::$app['LevAPP']) && isset($res['uid']) && $res['uid'] >0) {
            $res['Lev'] = UserLoginModelHelper::registerUid($res['uid'], $username, $password, false);
            UserLoginModelHelper::setLoginStatus($res['uid'], $password);
        }
        return $res;
    }

    /**
     * @param array $userInfo
     * @param string $ip
     * @param int $cookietime
     * @return array
     */
    public static function setLogin($userInfo, $ip = '', $cookietime = 2592000) {
        $res = Lev::getDB()->setLogin($userInfo, $ip, $cookietime);
        if (empty(Lev::$app['LevAPP']) && isset($res['uid']) && $res['uid'] >0) {
            empty($userInfo['username']) && $userInfo = UserHelper::userInfo($res['uid']);
            empty($userInfo['password']) && $userInfo['password'] = 1234560;
            $res['Lev'] = UserLoginModelHelper::registerUid($res['uid'], $userInfo['username'], $userInfo['password'], false);
            UserLoginModelHelper::setLoginStatus($res['uid'], $userInfo['password']);
        }
        return $res;
    }

    public static function doRegister($username, $password, $email, $questionid = '', $answer = '', $ip = '') {
        $res = Lev::getDB()->doRegister($username, $password, $email, $questionid, $answer, $ip);
        if (empty(Lev::$app['LevAPP']) && isset($res['uid']) && $res['uid'] >0) {
            $res['Lev'] = UserLoginModelHelper::registerUid($res['uid'], $username, $password, false);
            UserLoginModelHelper::setLoginStatus($res['uid'], $password);
        }
        return $res;
    }
    public static function onlyRegister($username, $password, $email, $questionid = '', $answer = '', $ip = '') {
        $res = Lev::getDB()->onlyRegister($username, $password, $email, $questionid, $answer, $ip);
        if (empty(Lev::$app['LevAPP']) && isset($res['uid']) && $res['uid'] >0) {
            $res['Lev'] = UserLoginModelHelper::registerUid($res['uid'], $username, $password, false);
            //UserLoginModelHelper::setLoginStatus($res['uid'], $password);
        }
        return $res;
    }
    public static function quickRegister($pre = 'QK') {
        $str = substr(Lev::$app['timestamp'], 1);
        $username = strtoupper($pre) . $str;
        $password = mt_rand(100000, 999999);
        $email = $str . '@'.strtolower($pre).'.cn';
        $msg = static::onlyRegister($username, $password, $email);
        if (isset($msg['succeed']) && !empty($msg['uid'])) {
            $msg['username'] = $username;
            $msg['password'] = $password;
            $msg['email'] = $email;
        }
        return $msg;
    }

    public static function setPassword($uid, $password) {
        $userinfo = static::userInfo($uid);
        if ($userinfo) {
            return Lev::getDB()->setPassword($userinfo['username'], $password);
        }
        return Lev::responseMsg(-1001, '密码设置失败，查无此用户');
    }


    public static function doLogout() {
        Lev::getDB()->doLogout();
    }

    public static function getGroups($field = 'groupid,grouptitle') {
        return Lev::getDB()->getGroups($field);
    }

    /**
     * @return array
     */
    public static function myInfo() {
        return isset(Lev::$app['myInfo']) ? Lev::$app['myInfo'] : (Lev::$app['myInfo'] = Lev::getDB()->getUserInfo(Lev::$app['uid']));
    }

    public static function userInfo($uid = 0) {
        static $users;
        return isset($users[$uid]) ? $users[$uid] : Lev::getDB()->getUserInfo($uid);
    }

    public static function getUsers($uids = 0, $keys = [], $intval = true, $unique = true) {
        return Lev::getDB()->getUsers($uids, $keys, $intval, $unique);
    }

    public static function checkPasswordError($uid, $password) {
        return Lev::getDB()->checkPasswordError($uid, $password);
    }

    public static function isNameExist($username) {
        return Lev::getDB()->isNameExist($username);
    }

    public static function avatar($uid = 0) {
        !$uid && $uid = Lev::$app['uid'];
        return static::getAvatar($uid) ?: static::avatarByUid($uid);
    }

    public static function avatarByUid($uid = 0) {
        !$uid && $uid = Lev::$app['uid'];
        if (is_dir($dir = static::getAvatarsDir(0) . ($s = $uid%2))) {
            $files = glob($dir . '/*.jpg');
            if ($total = count($files)) {
                return static::getAvatarsDir(1) . $s . '/' . ($uid % $total) . '.jpg';
            }
        }
        return Lev::getAlias('@assets/avatar/'.($uid%2).'.jpg');
    }

    public static function getAvatarsDir($web = true) {
        return Lev::$aliases[$web ? '@web' : '@webroot'] . Lev::$aliases['@appweb'] . '/data/avatars/';
    }

    public static function getAvatar($uid, $size = 'middle', $type = '') {
        $src = static::setAvatar($uid, $size, $type);
        if (is_file(Lev::$aliases['@webroot'] . $src)) {
            return Lev::$aliases['@web'] . $src;
        }
        return Lev::getDB()->getAvatar($uid, $size, $type);
    }
    public static function setAvatar($uid, $size = 'middle', $type = '') {
        $uid = sprintf("%09d", $uid);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);
        $typeadd = $type == 'real' ? '_real' : $type;
        return '/data/avatar/' .$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).$typeadd.'_avatar_'.$size.'.jpg';
    }
    public static function setAvatarData($src, $uid, $size = 'middle', $type = '', $force = false) {
        $avatar = static::setAvatar($uid, $size, $type);
        if (!$force && is_file(Lev::$aliases['@webroot'] . $avatar)) {
            return Lev::$aliases['@web'] . $avatar;
        }
        if (($data = file_get_contents($src)) && stripos($data, '</body>') === false) {
            $file = Lev::$aliases['@webroot'] . $avatar;
            cacheFileHelpers::mkdirv(dirname($file));
            file_put_contents($file, $data);
            return Lev::$aliases['@web'] . $avatar;
        }
        return '';
    }

}