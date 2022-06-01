<?php
/**
 * Copyright (c) 2021-2222   All rights reserved.
 *
 * 创建时间：2021-11-29 18:36
 *
 * 项目：levs  -  $  - UserLoginModelHelper.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');


namespace lev\helpers;

use Lev;
use lev\base\Modelv;
use lev\base\Requestv;

class UserLoginModelHelper extends Modelv
{

    public static $tableName = '{{%lev_users_login}}';

    public static $userStatus = [
        '正常',
        '关闭',
    ];

    public static function userStatus() {
        return static::$userStatus;
    }

    public static function checkPasswordError($uid, $password) {
        $user = static::findOne(['id'=>$uid]);
        if (empty($user)) {
            return Lev::responseMsg(-404, '抱歉，查无此用户');
        }elseif ($user['password'] !== static::md5Password($password, $user['safecode'])) {
            return Lev::responseMsg(-405, '抱歉，密码错误');
        }
        return false;
    }

    public static function setPassword($username, $password) {
        if ($userinfo = static::findOne(['username'=>$username])) {
            static::loginout($userinfo['id']);
            static::setLoginStatusCache($userinfo['id'], null, true);
            return static::update([
                'password' => static::md5Password($password, $safecode = static::setSafecode()),
                'safecode' => $safecode
            ], ['id'=>$userinfo['id']]) ? Lev::responseMsg(1) : Lev::responseMsg(-1);
        }
        return Lev::responseMsg(-1001, '密码设置失败，查无此用户');
    }

    public static function getUsers($uids) {
        is_array($uids) && $uids = implode(',', $uids);
        return $uids ? static::findAllField('id as uid,username', ['id IN ('.$uids.')'], 'uid') : [];
    }

    public static function registerUid($uid, $username, $password, $login = true, $groupid = 0, $adminid = 0)
    {
        return static::register($username, $password, $login, $groupid, $adminid, $uid);
    }

    /**
     * 注册成功必须返回UID
     * @param $username
     * @param $password
     * @param bool $login
     * @param int $groupid
     * @param int $adminid
     * @param int $uid
     * @return array
     */
    public static function register($username, $password, $login = true, $groupid = 0, $adminid = 0, $uid = 0) {
        $data = [];
        if ($uid >0) {
            if (static::findOne(['id'=>$uid])) {
                return Lev::responseMsg(-314, '抱歉，用户UID已被注册');
            }
            $data['id'] = $uid;
        }
        if ($errMsg = static::isNameExist($username, false)) {
            return $errMsg;
        }
        $ckuser = static::findOne(['username'=>$username]);
        if ($ckuser) {
            return Lev::responseMsg(-304, '抱歉，用户名已被注册');
        }
        $data += [
            'safecode' => $safecode = static::setSafecode(),
            'password' => $password = static::md5Password($password, $safecode),
            'username' => $username,
            'addtime'  => Lev::$app['timestamp'],
        ];
        if ($data['uid'] = static::insert($data, true)) {
            $data['id'] = $data['uid'];
            $data['groupid'] = $groupid;
            $data['adminid'] = $adminid;
            $data = UserHelper::safeColumns($data);
            if ($ck = UserHelper::findOne(['id'=>$data['id']])) {
                UserHelper::update($data, ['id'=>$data['id']]);
            }else {
                UserHelper::insert($data);
            }
            $login && static::setLoginStatus($data['id'], $password);
            return Lev::responseMsg(1, '注册成功1', ['info' => $data, 'uid'=>$data['id']]);
        }
        return Lev::responseMsg(-1, '抱歉，注册失败了');
    }

    public static function setSafecode() {
        return substr(md5(mt_rand(0, 100000000).microtime(true).mt_rand(0, 100000000).Lev::mtrandv(132)), 8, 8);
    }
    public static function md5Password($password, $safecode) {
        return md5(md5($password).md5($safecode.$password).md5(substr($password, -3)).substr(md5($password), 8, 16));
    }

    public static function login($username, $password, $uid = null) {
        $where = $uid === null ? ['username'=>$username] : ['id'=>$uid];
        $user = static::findOne($where);
        if (empty($user)) {
            return Lev::responseMsg(-404, '抱歉，查无此用户');
        }elseif ($user['password'] !== static::md5Password($password, $user['safecode'])) {
            return Lev::responseMsg(-405, '抱歉，密码错误');
        }elseif ($user['status']) {
            return Lev::responseMsg(-406, '抱歉，用户已被禁止');
        }
        static::setLoginStatus($user['id'], $password);
        return Lev::responseMsg(1, '登陆成功1', ['uid'=>$user['id']]);
    }

    public static function loginout($uid) {
        $authInfo = static::getAuthInfo();
        if (empty($authInfo[1]) || $authInfo[1] != $uid) {
            return Lev::responseMsg(-1, '失败，非法退出操作！'.$uid);
        }
        static::setLoginStatus($uid, $authInfo[0], -1);
        return Lev::responseMsg();
    }

    /**
     * @return int
     */
    public static function checkLogin() {
        $authkey = static::getAuthkey();
        $authInfo = Lev::authcodev($authkey, true);
        if ($authInfo && count($authInfo = explode("\t", $authInfo)) >1) {
            $authCache = static::getAuthCache($authInfo[1]);
            if (isset($authCache['pwds'][$authInfo[0]])) {
                Lev::$app['myInfo']        = $authCache['userDetail'];
                Lev::$app['myInfo']['uid'] = $authInfo[1];

                Lev::$app['username'] = $authCache['userDetail']['username'];
                Lev::$app['uid']      = $authInfo[1];
                if ($authCache[1] !== $authInfo[0] && !isset($authCache['ips'][$authCache[2]]) && $authCache[2] != Requestv::getRemoteIP()) {
                    $authCache['ips'][$authCache[2]] = 1;
                    static::setLoginStatusCache($authInfo[1], $authCache);
                    Lev::setNotices('【提示】账号已在【' . Lev::asRealTime($authCache[3]) . '】登陆IP：' . $authCache[2]);
                }
                $authInfo[2] < Lev::$app['timestamp'] - 3600 * 24 *7 && static::setLoginStatus($authInfo[1]);
                return $authInfo[1];
            }
        }
        return empty(Lev::$app['LevAPP']) ? Lev::$app['uid'] : 0;
    }

    public static function getAuthInfo() {
        return explode("\t", Lev::authcodev(static::getAuthkey(), true));
    }

    public static function getAuthkey() {
        return static::setLoginStatus();
    }

    public static function getAuthCache($uid) {
        return static::setLoginStatusCache($uid);
    }

    public static function setLoginStatus($uid = null, $password = null, $cookietime = null) {
        $authkey = 'authkeyv';
        if ($uid === null) {
            return Lev::opCookies($authkey);
        }

        $cookietime === null && $cookietime = 3600 * 24 * 30;
        if ($cookietime <1) {
            $cachePwd = '';
            $value = null;
        }else {
            if ($password === null) {
                if ($user = static::findOne(['id'=>$uid])) {
                    $password = $user['password'];
                }else {
                    return Lev::responseMsg(-4, '查无用户信息：'.$uid);
                }
            }

            $timestamp = Lev::$app['timestamp'];

            $cachePwd = md5(microtime(true).mt_rand(0, 10000));
            $password = md5($cachePwd.$password);
            $value = Lev::authcodev("{$password}\t{$uid}\t{$timestamp}");

            $userDetail = UserHelper::findOne(['id'=>$uid]);
            $data = [
                'uptime' => $timestamp,
                'ip' => Requestv::getRemoteIP(),
            ];
            UserHelper::update($data, ['id'=>$uid]);
        }

        static::setLoginStatusCache($uid, [
            $cachePwd,
            $password,
            Requestv::getRemoteIP(),
            $timestamp,
            'userDetail'=>$userDetail,
        ]);

        return Lev::opCookies($authkey, $value, $cookietime, 1, true)
            ? Lev::responseMsg(1, '登陆成功')
            : Lev::responseMsg(-1, '抱歉，登陆失败！检查是否开启COOKIE');
    }

    public static function setLoginStatusCache($uid, array $cachePwd = null, $clear = false) {
        $ckey = 'setLoginStatus'.$uid;
        if ($clear) {
            return UserCacheHelper::clearc($ckey);
        }
        is_array($login = UserCacheHelper::getUserSetting($uid, $ckey)) || $login = [];
        if ($cachePwd === null) {
            return $login;
        }
        if ($cachePwd[0]) {
            $login['pwds'][$cachePwd[1]] = $cachePwd[0];
            $cachePwd['pwds'] = array_slice($login['pwds'], -5, null, true);
        }else {
            unset($login['pwds'][$cachePwd[1]]);
            $cachePwd = $login;
        }
        return UserCacheHelper::setUserSetting($uid, $ckey, $cachePwd);
    }

    public static function updateUsername($uid, $username) {
        $ck = static::findOne(['id'=>$uid]);
        if (empty($ck)) {
            return static::registerUid($uid, $username, '', false);
        }
        return static::update(['username'=>$username], ['id'=>$uid]) &&
        UserHelper::update(['username'=>$username], ['id'=>$uid]);
    }

    public static function isNameExist($username, $ckexisit = true) {
        if ($username != Lev::stripTags($username)) {
            return Lev::responseMsg(-3041, '抱歉，用户名不合法');
        }
        if (Lev::strlenv($username) > 16) {
            return Lev::responseMsg(-3042, '抱歉，用户名过长');
        }
        if ($ckexisit && static::findOne(['username'=>$username])) {
            return Lev::responseMsg(-304, '抱歉，用户名已经存在');
        }
        return false;
    }

}