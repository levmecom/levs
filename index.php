<?php
/**
 * Copyright (c) 2021-2222   All rights reserved.
 *
 * 创建时间：2021-11-29 14:37
 *
 * 项目：levs  -  $  - index.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');

header("Content-type: text/html; charset=utf-8");//统一且固定使用utf-8编码 - 通过转码将数据库编码变得一致

if (is_file(__DIR__ . '/runtime/config.php')) {
    header('location:web', true, 302);
}else if (is_file(__DIR__ . '/web/install_lev.php')) {
    header('location:web/install_lev.php', true, 302);
}else {
    $downurl = 'https://dz.levme.com/levstore/view-levs.html';
    exit('抱歉，安装文件丢失，请重新下载！<a href="'.$downurl.'">点我下载</a>');
}