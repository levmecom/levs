<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-09-15 01:14
 *
 * 项目：rm  -  $  - levs_discuz.php
 *
 * 作者：liwei 
 */

!defined('INLEV') && exit('Access Denied LEV');

defined('DISABLEXSSCHECK') || define('DISABLEXSSCHECK', 1);//取消特殊字符验证，微信公众号推送含有特殊字符
defined('LEVS_GATE') || define('LEVS_GATE', 1);

//主要用于检查文件入口是否正确，防止直接访问文件。如果将/web目录设置为网站根目录，可忽略
defined('INLEV') || define('INLEV', 1);

if (is_file($_levs_ip_ban_file = __DIR__ .'/levs_ip_ban.php')) require $_levs_ip_ban_file;

//empty($_GET['id']) && $_GET['id'] = 'levs';

function ROUTE_ERROR_SHOW_MESSAGE__40404($errorMsg) {
    defined('ROUTE_ERROR_SHOW_MESSAGE') || define('ROUTE_ERROR_SHOW_MESSAGE', $errorMsg);
    include __DIR__ . '/source/plugin/levs/levs.inc.php';
    exit;
}

//include __DIR__ . '/plugin.php';


/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: plugin.php 27335 2012-01-16 10:15:37Z monkey $
 */

define('APPTYPEID', 127);
define('CURSCRIPT', 'plugin');
define('NOT_IN_MOBILE_API', 1);

require __DIR__ . '/source/class/class_core.php';

$discuz = C::app();

$cachelist = array('plugin', 'diytemplatename');

$discuz->cachelist = $cachelist;
$discuz->init();

global $_G;

if(!empty($_GET['id'])) {
    list($identifier, $module) = explode(':', $_GET['id']);
    $module = $module !== NULL ? $module : $identifier;
} else {
    //ROUTE_ERROR_SHOW_MESSAGE__40404('缺失GET参数id');
    //showmessage('plugin_nonexistence');

    include __DIR__ . '/source/plugin/levs/levs.inc.php';
    exit;
}
$mnid = 'plugin_'.$identifier.'_'.$module;
$pluginmodule = isset($_G['setting']['pluginlinks'][$identifier][$module]) ? $_G['setting']['pluginlinks'][$identifier][$module] : (isset($_G['setting']['plugins']['script'][$identifier][$module]) ? $_G['setting']['plugins']['script'][$identifier][$module] : array('adminid' => 0, 'directory' => preg_match("/^[a-z]+[a-z0-9_]*$/i", $identifier) ? $identifier.'/' : ''));

if(!preg_match('/^[\w\_]+$/', $identifier)) {
    ROUTE_ERROR_SHOW_MESSAGE__40404('插件不存在或已关闭1');
    //showmessage('plugin_nonexistence');
}

//if(empty($identifier) || !preg_match("/^[a-z0-9_\-]+$/i", $module) || !in_array($identifier, $_G['setting']['plugins']['available'])) {
//    ROUTE_ERROR_SHOW_MESSAGE__40404('插件不存在或已关闭2');
//    //showmessage('plugin_nonexistence');
//} else

if($pluginmodule['adminid'] && ($_G['adminid'] < 1 || ($_G['adminid'] > 0 && $pluginmodule['adminid'] < $_G['adminid']))) {
    ROUTE_ERROR_SHOW_MESSAGE__40404('你不是管理员，无权限访问');
    //showmessage('plugin_nopermission');
} elseif(!is_file(($modfile = __DIR__ . '/source/plugin/'.$pluginmodule['directory'].$module.'.inc.php'))) {
    ROUTE_ERROR_SHOW_MESSAGE__40404($identifier.'插件未安装或控制器文件丢失：source/plugin/'.$pluginmodule['directory'].$module.'.inc.php');
    //showmessage('plugin_module_nonexistence', '', array('mod' => $modfile));
}

define('CURMODULE', $identifier);
//runhooks();

include $modfile;
