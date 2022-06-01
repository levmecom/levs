<?php
/**
 * Copyright (c) 2022-2222   All rights reserved.
 *
 * 创建时间：2022-04-04 22:35
 *
 * 项目：levs  -  $  - toolbar.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');

//page.php include 文件

/* @var $toolbar */

if ($toolbar === null) {
    Lev::toolbar();
}else if (is_file($toolbar)) {
    include $toolbar;
}else {
    echo $toolbar;
}