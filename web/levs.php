<?php
/**
 * Copyright (c) 2021-2222   All rights reserved.
 *
 * 创建时间：2021-11-30 00:00
 *
 * 项目：levs  -  $  - levs.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');

defined('APPVROOT') or define('APPVROOT', dirname(__DIR__));

if (!defined('IN_DISCUZ') && !is_file(APPVROOT . '/runtime/.install.lock') && !is_file(APPVROOT . '/runtime/config.php')) {
    header('location:install_lev.php', true, 302);
    exit;
}

include __DIR__ .'/gate.php';