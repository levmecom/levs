<?php
/**
 * Copyright (c) 2022-2222   All rights reserved.
 *
 * 创建时间：2022-04-04 22:38
 *
 * 项目：levs  -  $  - toolbar.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');

//tab.php include 文件

/* @var $v array */


if (is_file($v['toolbar'])) {
    include $v['toolbar'];
}else if (isset($v['toolbar'])) {
    echo $v['toolbar'];
}