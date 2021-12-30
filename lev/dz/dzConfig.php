<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-04 21:35
 *
 * 项目：upload  -  $  - dzConfig.php
 *
 * 作者：liwei 
 */

use lev\helpers\dbHelper;

!defined('IN_DISCUZ') && exit('Access Denied'); //【审核注意】内部文件已经转化检查INLEV常量等同IN_DISCUZ

//入口 - APP主目录
//defined('APPVROOT') or define('APPVROOT', dirname(dirname(__DIR__)));

global $_G;

Lev::$app['charset'] = CHARSET;

Lev::$app['cookies']['pre']    = $_G['config']['cookie']['cookiepre'];
Lev::$app['cookies']['domain'] = $_G['config']['cookie']['cookiedomain'];
Lev::$app['cookies']['path']   = $_G['config']['cookie']['cookiepath'];


Lev::$app['isDiscuz'] = 1;
Lev::$app['homeFile'] = 'plugin.php';

Lev::$app['referer'] = dreferer();

Lev::$app['uid'] = $_G['uid'];
Lev::$app['groupid'] = $_G['groupid'];
Lev::$app['adminid'] = $_G['adminid'];
Lev::$app['username'] = dbHelper::getDataToCharset($_G['username']);
Lev::$app['myInfo'] = dbHelper::getDataToCharset($_G['member']);

Lev::$app['_csrf'] = formhash();

Lev::$app['SiteName'] = dbHelper::getDataToCharset($_G['setting']['bbname']);

Lev::$app['timestamp'] = TIMESTAMP;

Lev::$app['isAdmin'] = $_G['adminid'] == 1;

Lev::$app['version'] .= ' '.dbHelper::getDataToCharset($_G['setting']['version']);

Lev::$app['db'] = [
    'charset'  => $_G['config']['db'][1]['dbcharset'],
    'prefix'   => $_G['config']['db'][1]['tablepre'],
    'dbname'   => $_G['config']['db'][1]['dbname'],
];

Lev::$app['CnzzJs'] = Lev::setCnzzJs('dz_statcode', $_G['setting']['statcode']);
Lev::$app['Icp'] = dbHelper::getDataToCharset($_G['setting']['icp']);
Lev::$app['ip'] = \lev\base\Requestv::getRemoteIP();//$_G['clientip'];

Lev::$app['scoretypes'] = \lev\dz\discuzHelper::scoretypes();

//defined('IN_ADMINCP') && loadcache('plugin');
//Lev::$app['settings'] = $_G['cache']['plugin'];

Lev::$db = new \lev\dz\dzUserHelper();

$config['aliases'] = [
    '@appweb'     => '/source/plugin/'.basename(APPVROOT).'/web',
    '@assets'     => \lev\base\Requestv::getBaseUrl() . '/source/plugin/'.basename(APPVROOT).'/web/assets',
    '@appassets'  => \lev\base\Requestv::getBaseUrl() . '/source/plugin/'.basename(APPVROOT).'/assets',
    '@runtime'    => DISCUZ_ROOT . 'data/levruntime',
    '@settings'   => DISCUZ_ROOT . 'data/levruntime/settings',
    '@views'      => APPVROOT . '/template',
    '@renders'    => APPVROOT . '/template/renders',
    '@modules'    => dirname(APPVROOT), //DISCUZ_ROOT . 'source/plugin',
];

return $config;