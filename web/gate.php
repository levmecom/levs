<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-04 09:05
 *
 * 项目：upload  -  $  - index.php
 *
 * 作者：liwei 
 */
//前台入口文件

if (is_file($_levs_ip_ban_file = __DIR__ .'/levs_ip_ban.php')) require $_levs_ip_ban_file;

//defined('IN_DISCUZ') OR define('IN_DISCUZ', true);

//!defined('IN_DISCUZ') && exit('Access Denied'); //dzConfig.php 中有做核查，这里可以省略

//require dirname(dirname(__DIR__)) . '/lev/Lev.php'; //引入APP基类

defined('APPVROOT') or define('APPVROOT', dirname(__DIR__));

require_once APPVROOT . '/lev/Lev.php'; //引入APP基类

$config = defined('IN_DISCUZ')
    ? include APPVROOT . '/lev/dz/dzConfig.php' //配置DZ相关
    : include APPVROOT . '/lev/myConfig.php';

Lev::actionObjectMethod('Lev', [$config], 'init'); //初始化 - 公共参数、配置等数据

//\lev\base\Assetsv::registerApp(1);

\lev\base\Controllerv::toAction(); //路由控制器
