<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-03 22:27
 *
 * 项目：gitee  -  $  - Lev.php
 *
 * 作者：liwei 
 */

//通过引用此文件才能初始化应用 - 不可直接访问其它文件 - 直接无权访问

error_reporting(0);
INI_SET('display_errors', 1);ERROR_REPORTING(E_ALL ^ E_NOTICE);//开发环境

header("Content-type: text/html; charset=utf-8");//统一且固定使用utf-8编码 - 通过转码将数据库编码变得一致

use lev\helpers\UserLoginModelHelper;
use lev\LevHelper;
use lev\base\Requestv;
use lev\base\Modulesv;

require_once __DIR__ . '/LevHelper.php';

class Lev extends LevHelper
{

    /**
     * @param array $config
     */
    public static function init($config = ['aliases'=>[]]) {
        static::$aliases = $config['aliases'] + [ //以下参数通配置文件作出相应调整，移步配置文件查看真实值
                '@app'        => APPVROOT,
                '@appmodule'  => APPVROOT,
                '@views'      => APPVROOT . '/views',
                '@renders'    => APPVROOT . '/views/renders',
                '@widgets'    => APPVROOT . '/widgets',
                '@runtime'    => APPVROOT . '/runtime',
                '@settings'   => APPVROOT . '/runtime/settings',
                '@uploads'    => APPVROOT . '/web/data/ups',
                '@modules'    => APPVROOT . '/modules',

                '@layouts'    => __DIR__ . '/layouts',

                '@web'        => Requestv::getBaseUrl(), //域名指向根目录
                '@webroot'    => dirname(Requestv::getScriptFile()),//网站硬盘根目录
                '@html'       => Requestv::getBaseUrl().'/html', //静态html网页目录
                '@htmlroot'   => dirname(Requestv::getScriptFile()).'/html', //静态html网页目录
                '@host'       => Requestv::getHostName(), //eg: explame.com
                '@hostinfo'   => Requestv::getHostInfo(),//eg: https://explame.com
                '@siteurl'    => Requestv::getHostInfo() . Requestv::getBaseUrl(), //eg: https://explame.com/path
                '@assets'     => Requestv::getBaseUrl().'/assets',//静态文件目录
        ] + static::$aliases;

        static::initTimeZone();

        static::initIden();

        static::setLoginUid();

        static::initIsAdmin();
        static::initSettings();


        Lev::actionObjectMethodIden('levvv', 'modules\levvv\helpers\setHelper', [], 'isVipLink');
        static::initModule();

    }

    private static function initModule()
    {

        $iden = defined('MODULEIDEN') ? MODULEIDEN : '';
        static::setModule($iden);

        static::actionObjectMethod('modules\\'.Modulesv::getIdenNs(Lev::$app['iden']).'\\' . Lev::$app['iden'] . 'Helper', [], 'init');
    }

    private static function initIden() {
        if (!isset(static::$app['iden'])) {
            if ($iden = Lev::stripTags(Lev::GPv('id'))) {
                strpos($iden, ':') !== false && $iden = explode(':', $iden)[1];
                static::$app['iden'] = $iden;
            }else {
                static::$app['iden'] = Lev::SiteIden();
            }
        }

        defined('MODULEIDEN') or define('MODULEIDEN', static::$app['iden']);

        //!isset(static::$app['iden']) && static::$app['iden'] = basename(APPVROOT);
    }

    private static function initTimeZone() {
        if (!isset(static::$app['timestamp'])) {
            if (isset(static::$app['timeZone'])) {
                date_default_timezone_set(static::$app['timeZone']);
            } elseif (!ini_get('date.timezone')) {
                date_default_timezone_set('UTC');
            }
            static::$app['timestamp'] = time();
        }
    }

    private static function initIsAdmin() {
        static::$app['isAdmin'] === null && static::$app['isAdmin'] = static::$app['uid'] == 1;
    }

    private static function initSettings() {
        if (!empty(Lev::$app['isDiscuz']) && is_file(static::$aliases['@webroot'] . '/levs.php')) {
            static::$app['homeFile'] = 'levs.php';
        }
    }

    private static function setLoginUid()
    {
        //isset(Lev::$app['uid']) ||
        Lev::$app['uid']    = UserLoginModelHelper::checkLogin();
        Lev::$app['_csrf']  = Lev::getCsrf();
        Lev::$app['Icp'] = Lev::stget('Icp', 'levs');
    }

    public static function checkHideT()
    {
        return Lev::stget('SiteName', 'levs') == '-';
    }

}


spl_autoload_register(['Lev', 'autoload'], true, true);

