<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-06 14:53
 *
 * 项目：upload  -  $  - Adminv.php
 *
 * 作者：liwei 
 */

namespace lev\base;

use Lev;
use lev\controllers\LoginController;
use lev\helpers\cacheFileHelpers;
use lev\helpers\ModulesHelper;

!defined('INLEV') && exit('Access Denied LEV');


class Adminv
{
    public static $apiCheck = null;

    public static function checkAccess() {
        static::isAdmin() || Lev::showMessages('抱歉，您没有管理权限');
        defined('INADMINLEV') || define('INADMINLEV', 1);
    }

    public static function isAdmin() {
        return Lev::$app['isAdmin'] ?: static::apiCheck();
    }

    public static function definedISAPI() {
        defined('ISAPI') || define('ISAPI', 1);
    }

    public static function apiCheck() {
        if (static::$apiCheck !== null) {
            return static::$apiCheck;
        }

        static::$apiCheck = false;

        if ($adminSign = Lev::GETv('adminSign')) {
            $timestamp = floatval(Lev::GPv('timestamp'));
            if ($timestamp < Lev::$app['timestamp'] - 3600 * 24) {
                Lev::GETv('showErr') && exit('管理签名超时');
            }else {
                //$pwd = Lev::stgetv('adminPwd', Lev::$app['iden']);
                $pwd = Lev::actionObjectMethod('modules\\'.Modulesv::getIdenNs(Lev::$app['iden']).'\\'.Lev::$app['iden'].'Helper', [], 'adminPwd');
                if ($pwd) {
                    if (static::getAdminSign($pwd, $timestamp) === $adminSign) {
                        static::definedISAPI();
                        static::$apiCheck = true;
                    }else {
                        Lev::GETv('showErr') && exit('错误的管理签名');
                    }
                }
            }
        }

        return static::$apiCheck;
    }

    public static function getAdminSign($adminPwd, $timestamp) {
        return md5($adminPwd . '123o0' . $timestamp);
    }


    /**
     * @param bool $checkToken
     * @return bool|mixed|string
     *
     * @see LoginController::actionCheckTempToken()
     */
    public static function getTemporaryAccesstoken($checkToken = false, $key = 'tempToken=') {
        $ckey = 'getTemporaryAccesstoken';
        if ($checkToken) {
            $token = cacheFileHelpers::getc($ckey);
            cacheFileHelpers::clearc($ckey);
            return $token === $checkToken;
        }
        if (Lev::$app['isAdmin']) {
            $token = static::getAdminSign(Lev::$app['timestamp'], Lev::$app['timestamp']);
            cacheFileHelpers::setc($ckey, $token, 300);
            return $key.$token;
        }
        return false;
    }
}