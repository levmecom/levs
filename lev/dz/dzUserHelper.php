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

namespace lev\dz;

use Lev;
use lev\helpers\UserHelper;

!defined('INLEV') && exit('Access Denied LEV');


class dzUserHelper extends discuzHelper
{

    public static function doLogin($username, $password, $autologin, $questionid = '', $answer = '', $ip = '', $charset = 1) {
        return parent::doLogin($username, $password, $autologin ? 2592000 : 3600*24, $questionid, $answer, $ip ?: Lev::$app['ip'], $charset);
    }

    /**
     * @param array $userInfo
     * @param string $ip
     * @param int $cookietime
     * @return array
     */
    public static function setLogin($userInfo, $ip = '', $cookietime = 2592000) {
        return parent::setLogin($userInfo, $ip, $cookietime);
    }

    public static function doRegister($username, $password, $email, $questionid = '', $answer = '', $ip = '', $doLogin = true) {
        return parent::doRegister($username, $password, $email, $questionid, $answer, $ip ?: Lev::$app['ip'], $doLogin);
    }
    public static function onlyRegister($username, $password, $email, $questionid = '', $answer = '', $ip = '') {
        return parent::onlyRegister($username, $password, $email, $questionid, $answer, $ip ?: Lev::$app['ip']);
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

//    public static function setPassword($uid, $password) {
//        $userinfo = static::userInfo($uid);
//        return $userinfo ? parent::setPassword($userinfo['username'], $password) : Lev::responseMsg(-1001, '密码设置失败，查无此用户');
//    }


    public static function doLogout() {
        discuzHelper::doLogout();
    }

    public static function getGroups($field = 'groupid,grouptitle') {
        return parent::getGroups($field);
    }

    /**
     * @return mixed
     */
    public static function myInfo() {
        return isset(Lev::$app['myInfo']) ? Lev::$app['myInfo'] : (Lev::$app['myInfo'] = parent::getUserInfo(Lev::$app['uid']));
    }

    public static function userInfo($uid = 0) {
        static $users;
        return isset($users[$uid]) ? $users[$uid] : parent::getUserInfo($uid);
    }

//    public static function getUsers($uids = 0, $keys = [], $intval = true, $unique = true) {
//        $keys && is_array($uids) && $uids = Lev::getArrayColumn($uids, $keys, $intval, $unique);
//        return parent::getUsers($uids);
//    }

}