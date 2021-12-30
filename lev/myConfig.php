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

//入口 - APP主目录
//defined('APPVROOT') or define('APPVROOT', dirname(dirname(__DIR__)));

defined('APPVIDEN') or define('APPVIDEN', 'levs');

defined('LEVROOT') or define('LEVROOT', __DIR__);

defined('APPVROOT') or define('APPVROOT', dirname(__DIR__));

$_dbconfig = include dirname(__DIR__) . '/runtime/config.php';
is_array($_dbconfig) && Lev::$app = $_dbconfig + Lev::$app;

//独立安装标识符。非安装在三方系统下，如：Discuz! phpwind 等
Lev::$app['LevAPP'] = 2;

Lev::$app['charset'] = 'utf-8';

Lev::$app['cookies']['domain'] = '';
Lev::$app['cookies']['path']   = '/';

//Lev::$app['isDiscuz'] = null;
Lev::$app['homeFile'] = 'index.php';

Lev::$app['uid'] = null;
Lev::$app['groupid'] = null;
//Lev::$app['username'] = '';
//Lev::$app['myInfo'] = [];

//Lev::$app['_csrf'] = Lev::getCsrf();

//Lev::$app['timestamp'] = TIMESTAMP;

//Lev::$app['isAdmin'] = $_G['adminid'] == 1;

//Lev::$app['version'] .= ' '.dbHelper::getDataToCharset($_G['setting']['version']);

// ----------------------------  CONFIG DB  ----------------------------- //
$_config['db']['1']['dbhost']    = Lev::$app['db']['dbhost'];
$_config['db']['1']['dbuser']    = Lev::$app['db']['username'];
$_config['db']['1']['dbpw']      = Lev::$app['db']['password'];
$_config['db']['1']['dbcharset'] = Lev::$app['db']['charset'];
$_config['db']['1']['pconnect']  = '0';
$_config['db']['1']['dbname']    = Lev::$app['db']['dbname'];
$_config['db']['1']['tablepre']  = Lev::$app['db']['prefix'];
$_config['db']['slave']          = '';
$_config['db']['common']['slave_except_table'] = '';

Lev::$app['db']['dzconfig'] = $_config['db'];

//Lev::$app['CnzzJs'] = Lev::setCnzzJs('dz_statcode', $_G['setting']['statcode']);
//Lev::$app['Icp'] = Lev::stget('Icp', 'levs');
Lev::$app['ip'] = \lev\base\Requestv::getRemoteIP();//$_G['clientip'];
Lev::$app['referer'] = \lev\base\Requestv::getReferer();

Lev::$app['scoretypes'] = [];//\lev\dz\discuzHelper::scoretypes();

//defined('IN_ADMINCP') && loadcache('plugin');
//Lev::$app['settings'] = $_G['cache']['plugin'];

//Lev::$db = new \lev\dz\dzUserHelper();

$config['aliases'] = [
//    '@appweb'     => '/source/plugin/'.basename(APPVROOT).'/web',
//    '@assets'     => \lev\base\Requestv::getBaseUrl() . '/source/plugin/'.basename(APPVROOT).'/web/assets',
//    '@appassets'  => \lev\base\Requestv::getBaseUrl() . '/source/plugin/'.basename(APPVROOT).'/assets',
//    '@runtime'    => DISCUZ_ROOT . 'data/levruntime',
//    '@settings'   => DISCUZ_ROOT . 'data/levruntime/settings',
    '@views'      => APPVROOT . '/template',
    '@renders'    => APPVROOT . '/template/renders',
    '@modules'    => dirname(APPVROOT),
];

return $config;