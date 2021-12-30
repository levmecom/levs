<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-23 10:03
 *
 * 项目：upload  -  $  - levmodules.inc.php
 *
 * 作者：liwei 
 */

//组件路由文件入口

!defined('IN_DISCUZ') && exit('Access Denied LEV');

//定义模块标识 - 非前置应用无需定义
defined('MODULEIDEN') or define('MODULEIDEN', 'ipban');

require __DIR__ . '/iden.const.php';

require_once APPVROOT . '/web/index.php';
