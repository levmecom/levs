<?php
/**
 * Copyright (c) 2021-2222   All rights reserved.
 *
 * 创建时间：2021-11-30 17:04
 *
 * 项目：levs  -  $  - admin.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');

defined('INADMIN_GATE') || define('INADMIN_GATE', 1);

$_GET['id'] = 'levs';
$_GET['r'] = 'admin-modules';

include __DIR__ . '/levs.php';