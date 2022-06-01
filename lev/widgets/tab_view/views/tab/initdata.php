<?php
/**
 * Copyright (c) 2022-2222   All rights reserved.
 *
 * 创建时间：2022-04-04 22:47
 *
 * 项目：levs  -  $  - initdata.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');

//tab.php include 文件

/* @var $v array */

if (is_file($v['initData'])) {
    include $v['initData'];
}else if (isset($v['initData'])) {
    echo $v['initData'];
}
