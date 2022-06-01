<?php
/**
 * Copyright (c) 2022-2222   All rights reserved.
 *
 * 创建时间：2022-04-04 22:38
 *
 * 项目：levs  -  $  - footer.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');

//tab.php include 文件

/* @var $v array */


//if ($v['footer'] === null) {
//    Lev::footer([], false);
//}else
if (is_file($v['footer'])) {
    include $v['footer'];
}else {
    echo $v['footer'];
}




